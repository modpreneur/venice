<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 11:49
 */

namespace AppBundle\Services;


use AppBundle\Entity\Invoice;
use AppBundle\Entity\User;
use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NecktieGateway
{
    const NECKTIE_OAUTH_AUTH_URI = "/oauth/v2/auth";
    const NECKTIE_OATH_TOKEN_URI = "/oauth/v2/token";
    const NECKTIE_USER_PROFILE_URI = "/api/v1/profile";
    const NECKTIE_USER_INVOICES_URI = "/api/v1/invoices";
    const NECKTIE_PRODUCT_ACCESSES_URI = "/api/v1/product-accesses";

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
     * @var ProductAccessManager
     */
    protected $productAccessManager;

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
        $productAccessManager,
        $tokenStorage,
        $router,
        $helper
    )
    {
        $this->entityManager = $entityManager;
        $this->connector = $connector;
        $this->container = $container;
        $this->productAccessManager = $productAccessManager;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->helper = $helper;

        $this->necktieUrl = $this->container->getParameter("necktie_url");
    }

    public function getRedirectUrlToNecktieLogin()
    {
        $necktieUrl = $this->necktieUrl . self::NECKTIE_OAUTH_AUTH_URI;
        $this->stateCookie = new Cookie(self::STATE_COOKIE_NAME, hash("sha256", rand() + time()));

        $queryParameters = [
            "client_id" => $this->container->getParameter("necktie_client_id"),
            "client_secret" => $this->container->getParameter("necktie_client_secret"),
            "redirect_uri" => $this->router->generate($this->container->getParameter("login_response_route")),
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
     *
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
                $user = new User();
                $user->setUsername($userInfo["username"])
                     ->setEmail($userInfo["email"])
                     ->setNecktieId($userInfo["id"]);

                if($persistNewUser)
                {
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            }
        }

        return null;
    }

    public function updateProductAccesses(User $user)
    {
        $this->refreshAccessTokenIfNeeded($user);

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

                $this->productAccessManager->giveAccessToProduct($user, $product, $dateFrom, $dateTo);
            }
        }
    }

    public function getInvoices(User $user)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $response = $this->getParsedResponse($user, self::NECKTIE_USER_INVOICES_URI, ["withItems" => true]);

        $invoices = [];
        foreach($response["invoices"] as $invoice)
        {
            $invoiceObject = new Invoice();

            if(array_key_exists("price_total", $invoice))
            {
                $invoiceObject->setTotalPrice($invoice["price_total"]);
            }

            if(array_key_exists("transaction_type", $invoice))
            {
                $invoiceObject->setTransactionType($invoice["transaction_type"]);
            }

            if(array_key_exists("transaction_time", $invoice))
            {
                $date = \DateTime::createFromFormat(\DateTime::W3C, $invoice["transaction_time"]);
                $invoiceObject->setTransactionType($date);
            }

            if(array_key_exists("items", $invoice))
            {
                foreach($invoice["items"] as $invoiceItem)
                {
                    if(array_key_exists("product", $invoiceItem) && array_key_exists("name", $invoiceItem["product"]))
                    {
                        $invoiceObject->addItem($invoiceItem["product"]["name"]);
                    }
                }

            }

            $invoices[] = $invoiceObject;
        }

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


    protected function getParsedResponse(User $user, $uri, $queryParameters = [])
    {
        $accessToken = $user->getLastAccessToken();

        if(!$accessToken)
        {
            return null;
        }

        $necktieUrl = $this->necktieUrl . $uri;

        $response = json_decode($this->connector->get($necktieUrl, $accessToken, $queryParameters), true);

        if(!$response || !$this->helper->isResponseOk($response))
        {
            throw new UnsuccessfulNecktieResponseException("Necktie access token has expired or is invalid.");
        }

        return $response;
    }


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

        if(!$this->helper->isRefreshTokenExpiredResponse($response))
        {
            $token = $this->helper->createAccessTokenFromArray($response);

            $user->addOAuthToken($token);
        }
        else
        {
            throw new UnsuccessfulNecktieResponseException("Necktie refresh token has expired.");
        }
    }

    public function getHelper()
    {
        return $this->helper;
    }

    public function refreshAccessTokenIfNeeded(User $user)
    {
        if(!$user->isLastAccessTokenValid())
        {
            $this->refreshAccessToken($user);
        }
    }

}