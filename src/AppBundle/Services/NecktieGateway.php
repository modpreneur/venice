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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\RouterInterface;

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
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var NecktieGatewayHelper
     */
    protected $helper;

    /**
     * @var  Client
     */
    protected $client;

    /** @var  Cookie */
    protected $stateCookie;

    /** @var string */
    protected $necktieUrl;

    public function __construct(
        $container,
        $entityManager,
        $router,
        $helper
    )
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->router = $router;
        $this->helper = $helper;

        $this->necktieUrl = $this->container->getParameter("necktie_url");
        if (!$this->necktieUrl) {
            throw new \Exception("Please define parameter: \"necktie_url\"");
        }

        $this->client = new Client(["base_uri" => $this->necktieUrl]);
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
        $necktieUrl = $this->necktieUrl.self::NECKTIE_OAUTH_AUTH_URI;
        $this->stateCookie = new Cookie(self::STATE_COOKIE_NAME, hash("sha256", rand() + time()));
        $router = $this->router;

        $queryParameters = [
            "client_id" => $this->container->getParameter("necktie_client_id"),
            "client_secret" => $this->container->getParameter("necktie_client_secret"),
            "redirect_uri" => $this->router->generate(
                $this->container->getParameter("login_response_route"),
                [],
                $router::ABSOLUTE_URL)
            ,
            "grant_type" => "trusted_authorization",
            "state" => $this->stateCookie->getValue()
        ];

        $queryParametersString = http_build_query($queryParameters);

        return $necktieUrl."?".$queryParametersString;
    }


    /**
     * Call necktie and get User entity.
     * This method should be typically called only in the login process.
     *
     * @param string $accessToken
     * @param bool $createNewUser
     * @param bool $persistNewUser
     *
     * @return User|null
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    public function getUserByAccessToken($accessToken = null, $createNewUser = false, $persistNewUser = true)
    {
        if (!is_string($accessToken)) {
            throw new \InvalidArgumentException("Access token has to be valid string");
        }

        $responseData = $this->getParsedResponse(null, "GET", self::NECKTIE_USER_PROFILE_URI, [], $accessToken);

        $userInfo = $this->helper->getUserInfoFromNecktieProfileResponse($responseData);

        if ($userInfo) {
            $user = $this
                ->entityManager
                ->getRepository("AppBundle:User")
                ->findOneBy(
                    ["username" => $userInfo["username"]]
                );

            if ($user) {
                return $user;
            } else if ($createNewUser) {
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

        $response = $this->getParsedResponse($user, "GET", self::NECKTIE_PRODUCT_ACCESSES_URI);

        foreach ($response["productAccesses"] as $productAccess) {
            if (is_array($productAccess) && array_key_exists("product", $productAccess) && array_key_exists("from_date", $productAccess)) {
                $dateTo = null;
                $product = $this
                    ->entityManager
                    ->getRepository("AppBundle:Product\\Product")
                    ->findOneBy(
                        ["id" => $productAccess["product"]]
                    );

                if (!$product) {
                    continue;
                }

                $dateFrom = \DateTime::createFromFormat(\DateTime::W3C, $productAccess["from_date"]);

                if (array_key_exists("to_date", $productAccess)) {
                    $dateTo = \DateTime::createFromFormat(\DateTime::W3C, $productAccess["to_date"]);
                }

                if (!$dateFrom instanceof \DateTime)
                    continue;
                if (!$dateTo instanceof \DateTime)
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

        $response = $this->getParsedResponse($user, "GET", self::NECKTIE_USER_INVOICES_URI, ["withItems" => true]);

        if (!is_array($response) || !array_key_exists("invoices", $response)) {
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
        $response = $this->getParsedResponse(
            $user,
            "POST",
            self::NECKTIE_OATH_TOKEN_URI,
            [
                "client_id" => $this->container->getParameter("necktie_client_id"),
                "client_secret" => $this->container->getParameter("necktie_client_secret"),
                "grant_type" => "refresh_token",
                "refresh_token" => $user->getLastRefreshToken()

            ],
            true
        );

        if (!$this->helper->isRefreshTokenExpiredResponse($response)) {
            $token = $this->helper->createAccessTokenFromArray($response);

            if ($token) {
                $user->addOAuthToken($token);
                $token->setUser($user);
            } else {
                throw new \Exception("Could not get token from response. Have you configured necktie client id and secret?");
            }
        } else {
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
        if (!$user->isLastAccessTokenValid() || ($dateDiff->h < 0 && $dateDiff->m < 10)) {
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

        $response = $this->getParsedResponse($user, "GET", $necktieUrl);

        if (is_array($response) && array_key_exists("billing plan", $response)) {
            return $this->getBillingPlanFromResponse($response["billing plan"]);
        }

        return null;
    }


    /**
     * Get billing all billing plans for given product.
     *
     * @param User $user
     * @param StandardProduct $product
     *
     * @return BillingPlan[]
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getBillingPlans(User $user, StandardProduct $product)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = str_replace("{productId}", $product->getNecktieId(), self::NECKTIE_PRODUCT_BILLING_PLANS_URI);
        $response = $this->getParsedResponse($user, "GET", $necktieUrl);
        $billingPlans = [];

        if (is_array($response) && array_key_exists("billing plans", $response)) {
            foreach ($response["billing plans"] as $billingPlanArray) {
                $billingPlans[] = $this->helper->getBillingPlanFromResponse($billingPlanArray);
            }

            return $billingPlans;
        }

        return null;
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

        if ($persist) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }


    /**
     * @param User $user
     * @param $method string
     * @param $uri string
     * @param array $parameters
     * @param null $accessToken
     * @param bool $isJson
     *
     * @return null|string
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    protected function getParsedResponse(User $user = null, $method, $uri, $parameters = [], $accessToken = null, $isJson = false)
    {
        if ($user) {
            $accessToken = $user->getLastAccessToken();
        } else if (!$accessToken) {
            throw new \Exception("No user nor access token provider");
        }

        $options = ["headers" => ["Authorization" => "Bearer {$accessToken}"],];

        if (strtolower($method) === "get") {
            $options["query"] = $parameters;
        } else if (strtolower($method) === "post") {
            if ($isJson) {
                $options["json"] = $parameters;
            } else {
                $options["form_params"] = $parameters;
            }
        }

        $response = null;
        try {
            $response = $this->client->request(
                $method,
                $uri,
                $options
            );
        } catch (ServerException $e) {
            throw new UnsuccessfulNecktieResponseException($e->getResponse()->getBody()->getContents());
        }

        $responseData = json_decode($response->getBody()->getContents(), true);

        return $responseData;
    }
}