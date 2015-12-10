<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 11:49
 */

namespace AppBundle\Services;


use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use AppBundle\Exceptions\ExpiredRefreshTokenException;
use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use AppBundle\Interfaces\NecktieGatewayInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NecktieGateway implements NecktieGatewayInterface
{
    const NECKTIE_OAUTH_AUTH_URI = "/oauth/v2/auth";
    const NECKTIE_OATH_TOKEN_URI = "/oauth/v2/token";
    const NECKTIE_USER_PROFILE_URI = "/api/v1/profile";
    const NECKTIE_USER_INVOICES_URI = "/api/v1/invoices";
    const NECKTIE_PRODUCT_ACCESSES_URI = "/api/v1/product-accesses";
    const NECKTIE_BILLING_PLAN_URI = "/api/v1/billing-plan/{id}";
    const NECKTIE_PRODUCT_BILLING_PLANS_URI = "/api/v1/product/{productId}/billing-plans";

    const STATE_COOKIE_NAME = "state";

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var NecktieGatewayHelper
     */
    protected $helper;

    protected $stateCookie;
    protected $necktieUrl;

    public function __construct(
        $container,
        $entityManager,
        $connector,
        $tokenStorage,
        $router,
        $helper
    )
    {
        $this->entityManager = $entityManager;
        $this->connector = $connector;
        $this->container = $container;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->helper = $helper;

        $this->necktieUrl = $this->container->getParameter("necktie_url");
    }


    /**
     * Do not use for redirecting to login form! Redirect to "login_route" route instead!
     *
     * @internal
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $necktieUrl = $this->necktieUrl . self::NECKTIE_OAUTH_AUTH_URI;
        $this->stateCookie = new Cookie(self::STATE_COOKIE_NAME, hash("sha256", rand() + time()));

        $queryParameters = [
            "client_id" => $this->container->getParameter("necktie_client_id"),
            "client_secret" => $this->container->getParameter("necktie_client_secret"),
            "redirect_uri" => $this->router->generate($this->container->getParameter("login_response_route"), [], $this->router::ABSOLUTE_URL),
            "grant_type" => "trusted_authorization",
            "state" => $this->stateCookie->getValue()
        ];

        $queryParametersString = http_build_query($queryParameters);

        return $necktieUrl . "?" .  $queryParametersString;
    }


    /**
     * Call necktie and get User entity.
     * This method should be typically called only in the login process.
     *
     * @param string     $accessToken
     * @param bool|false $createNewUser
     * @param bool|true  $persistNewUser
     *
     * @return User|null
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getUserByAccessToken($accessToken = null, $createNewUser = false, $persistNewUser = true)
    {
        if(!$accessToken)
        {
            throw new \InvalidArgumentException("Access token has to be valid string");
        }

        $necktieUrl = $this->necktieUrl . self::NECKTIE_USER_PROFILE_URI;
        $response = $this->connector->get($necktieUrl, $accessToken);

        if(!$this->helper->isResponseOk($response))
        {
            throw new UnsuccessfulNecktieResponseException();
        }

        $userInfo = $this->helper->getUserInfoFromNecktieProfileResponse($response);

        if($userInfo)
        {
            $user = $this
                ->entityManager
                ->getRepository("AppBundle:User")
                ->findOneBy(
                    ["username" => $userInfo["username"]]
                );

            if($user)
            {
                return $user;
            }
            else if($createNewUser)
            {
                return $this->createANewUser($userInfo, $persistNewUser);
            }
        }

        return null;
    }


    /**
     * Update product accesses for given user.
     *
     * @param User $user
     *
     * @throws UnsuccessfulNecktieResponseException
     *
     * @return \AppBundle\Entity\ProductAccess[]|void
     */
    public function updateProductAccesses(User $user)
    {
        $this->refreshAccessTokenIfNeeded($user);
        $givenProductAccesses = [];

        $response = $this->getParsedResponse($user, self::NECKTIE_PRODUCT_ACCESSES_URI);

        foreach($response["productAccesses"] as $productAccess)
        {
            if(array_key_exists("product", $productAccess) && array_key_exists("from_date", $productAccess))
            {
                $dateTo = null;
                $product = $this
                    ->entityManager
                    ->getRepository("AppBundle:Product\\Product")
                    ->findOneBy(
                        ["id" => $productAccess["product"]]
                    );

                if(!$product)
                {
                    continue;
                }

                $dateFrom = \DateTime::createFromFormat(\DateTime::W3C, $productAccess["from_date"]);

                if(array_key_exists("to_date", $productAccess))
                {
                    $dateTo = \DateTime::createFromFormat(\DateTime::W3C, $productAccess["to_date"]);
                }

                if(!$dateFrom instanceof \DateTime)
                    continue;
                if(!$dateTo instanceof \DateTime)
                    $dateTo = null;

                $givenProductAccesses[] = $user->giveAccessToProduct($product, $dateFrom, $dateTo, $productAccess["id"]);
            }
        }

        return $givenProductAccesses;
    }


    /**
     * @param User $user
     *
     * @return array
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getInvoices(User $user)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $response = $this->getParsedResponse($user, self::NECKTIE_USER_INVOICES_URI, ["withItems" => true]);

        if(!array_key_exists("invoices", $response))
        {
            return [];
        }

        $invoices = $this->helper->getInvoicesFromNecktieResponse($response);

        return $invoices;
    }

    public function getNewsletters(User $user)
    {
        $this->refreshAccessTokenIfNeeded($user);
        //todo: implement method
    }


    /**
     * @return mixed
     */
    public function getStateCookie()
    {
        return $this->stateCookie;
    }


    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     */
    public function refreshAccessToken(User $user)
    {
        $response = $this->connector->postAndGetJson(
            $this->necktieUrl . self::NECKTIE_OATH_TOKEN_URI,
            [
                "client_id" => $this->container->getParameter("necktie_client_id"),
                "client_secret" => $this->container->getParameter("necktie_client_secret"),
                "grant_type" => "refresh_token",
                "refresh_token" => $user->getLastRefreshToken()
            ]
        );

        if(!$response)
        {
            throw new Exception("Necktie response error");
        }

        if($this->helper->isInvalidClientResponse($response))
        {
            throw new \Exception("Invalid oauth client id and secret!");
        }

        if(!$this->helper->isRefreshTokenExpiredResponse($response))
        {
            $token = $this->helper->createAccessTokenFromArray($response);

            if($token)
                $user->addOAuthToken($token);
            else
                throw new \Exception("Could not get token from response. Have you configured necktie client id and secret?");

        }
        else
        {
            throw new ExpiredRefreshTokenException("Necktie refresh token has expired.");
        }
    }


    /**
     * @return NecktieGatewayHelper
     */
    public function getHelper()
    {
        return $this->helper;
    }


    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     */
    public function refreshAccessTokenIfNeeded(User $user)
    {
        $dateDiff = $user->getLastToken()->getValidTo()->diff(new \DateTime());

        // if the access token is expired or will expire in 10 minutes
        if(!$user->isLastAccessTokenValid() || ($dateDiff->h < 0 && $dateDiff->m < 10))
        {
            $this->refreshAccessToken($user);
        }
    }


    /**
     * Get billing plan by id
     *
     * @param User $user
     * @param      $id
     *
     * @return BillingPlan
     */
    public function getBillingPlan(User $user, $id)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = str_replace("{id}", $id, self::NECKTIE_BILLING_PLAN_URI);

        $response = $this->getParsedResponse($user, $necktieUrl);

        if(array_key_exists("billing plan", $response))
        {
            return $this->getBillingPlanFromResponse($response["billing plan"]);
        }

        return null;
    }


    /**
     * Get billing all billing plans for given product.
     *
     * @param User            $user
     * @param StandardProduct $product
     *
     * @return BillingPlan[]
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getBillingPlans(User $user, StandardProduct $product)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = str_replace("{productId}", $product->getNecktieId(), self::NECKTIE_PRODUCT_BILLING_PLANS_URI);
        $response = $this->getParsedResponse($user, $necktieUrl);
        $billingPlans = [];

        if(array_key_exists("billing plans", $response))
        {
            foreach($response["billing plans"] as $billingPlanArray)
            {
                $billingPlans[] = $this->getBillingPlanFromResponse($billingPlanArray);
            }

            return $billingPlans;
        }

        return null;
    }


    /**
     * @param $response
     *
     * @return BillingPlan|null
     */
    public function getBillingPlanFromResponse($response)
    {
        $billingPlan = new BillingPlan();

        if(array_key_exists("initial_price", $response))
        {
            $billingPlan->setInitialPrice($response["initial_price"]);
        }

        if(array_key_exists("id", $response))
        {
            $billingPlan->setNecktieId($response["id"]);
        }

        if(array_key_exists("id", $response))
        {
            $billingPlan->setId($response["id"]);
        }

        if(array_key_exists("type", $response) && $response["type"] == "recurring")
        {
            if(array_key_exists("rebill_price", $response))
            {
                $billingPlan->setRebillPrice($response["rebill_price"]);
            }

            if(array_key_exists("frequency", $response))
            {
                $billingPlan->setFrequency($response["frequency"]);
            }

            if(array_key_exists("rebill_times", $response))
            {
                $billingPlan->setRebillTimes($response["rebill_times"]);
            }
        }

        return $billingPlan;
    }


    /**
     * @param $userInfo
     * @param $persist
     *
     * @return User
     */
    protected function createANewUser($userInfo, $persist)
    {
        $user = new User();
        $user->setUsername($userInfo["username"])
             ->setEmail($userInfo["email"])
             ->setNecktieId($userInfo["id"]);

        if($persist)
        {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }


    /**
     * @param User  $user
     * @param       $uri
     * @param array $queryParameters
     *
     * @return mixed|null
     * @throws UnsuccessfulNecktieResponseException
     */
    protected function getParsedResponse(User $user, $uri, $queryParameters = [])
    {
        $accessToken = $user->getLastAccessToken();

        if(!$accessToken)
        {
            return null;
        }

        $necktieUrl = $this->necktieUrl . $uri;

        $rawResponse = $this->connector->get($necktieUrl, $accessToken, $queryParameters);
        $response = json_decode($rawResponse, true);

        if(!$response || !$this->helper->isResponseOk($response))
        {
            throw new UnsuccessfulNecktieResponseException($rawResponse);
        }

        return $response;
    }
}