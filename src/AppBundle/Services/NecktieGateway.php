<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 11:49
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;

class NecktieGateway
{
    const NECKTIE_OAUTH_AUTH_URI = "/oauth/v2/auth";
    const NECKTIE_USER_PROFILE_URI = "/api/v1/profile";
    const NECKTIE_PRODUCT_ACCESSES_URI = "/api/v1/product-accesses";

    const STATE_COOKIE_NAME = "state";

    protected $entityManager;
    protected $connector;
    protected $container;
    protected $productAccessManager;
    protected $tokenStorage;
    protected $router;

    protected $stateCookie;
    protected $necktieUrl;

    public function __construct(
        $container,
        $entityManager,
        $connector,
        $productAccessManager,
        $tokenStorage,
        $router
    )
    {
        $this->entityManager = $entityManager;
        $this->connector = $connector;
        $this->container = $container;
        $this->productAccessManager = $productAccessManager;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;

        $this->necktieUrl = $this->container->getParameter("necktie_url");
    }

    public function getRedirectUrlToNecktieLogin()
    {
        $necktieUrl = $this->necktieUrl . self::NECKTIE_OAUTH_AUTH_URI;
        $this->stateCookie = new Cookie(self::STATE_COOKIE_NAME, hash("sha256", rand() + time()));

        $queryParameters = [
            "client_id" => $this->container->getParameter("necktie_client_id"),
            "client_secret" => $this->container->getParameter("necktie_client_secret"),
            "redirect_uri" => $this->router->generate("core_login_response"),
            "grant_type" => "trusted_authorization",
            "state" => $this->stateCookie->getValue()
        ];

        $queryParametersString = http_build_query($queryParameters);

        return $necktieUrl . "?" .  $queryParametersString;
    }

    public function getUserByAccessToken($accessToken = null, $createNewUser = false, $persistNewUser = true)
    {
        if(!$accessToken)
        {
            throw new \InvalidArgumentException("Access token has to be valid string");
        }

        $necktieUrl = $this->necktieUrl . self::NECKTIE_USER_PROFILE_URI;

        //call necktie
        $response = $this->connector->get($necktieUrl, $accessToken);
        $userInfo = $this->getUserInfoFromNecktieProfileResponse($response);

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

    public function updateProductAccesses($accessToken)
    {
        if(!$accessToken)
        {
            throw new \InvalidArgumentException("Access token has to be valid string");
        }

        $necktieUrl = $this->necktieUrl . self::NECKTIE_PRODUCT_ACCESSES_URI;

        $response = json_decode($this->connector->get($necktieUrl, $accessToken), true);

        $user = $this->tokenStorage->getToken()->getUser();

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


    /**
     * @return mixed
     */
    public function getStateCookie()
    {
        return $this->stateCookie;
    }


    protected function getUserInfoFromNecktieProfileResponse($response)
    {
        $requiredFields = ["username", "email", "id"];
        $userInfo = [];
        $responseContent = json_decode($response, true);

        if(array_key_exists("user", $responseContent))
        {
            $responseContent = $responseContent["user"];
        }

        foreach($requiredFields as $requiredFiled)
        {
            if(array_key_exists($requiredFiled, $responseContent))
            {
                $userInfo[$requiredFiled] = $responseContent[$requiredFiled];
            }
            else
            {
                return null;
            }
        }

        return $userInfo;
    }

}