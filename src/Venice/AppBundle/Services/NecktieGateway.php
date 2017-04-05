<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 11:49.
 */
namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\RouterInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Venice\AppBundle\Interfaces\NecktieGatewayHelperInterface;
use Venice\AppBundle\Interfaces\NecktieGatewayInterface;

class NecktieGateway implements NecktieGatewayInterface
{
    const NECKTIE_OAUTH_AUTH_URI = 'oauth/v2/auth';
    const NECKTIE_OATH_TOKEN_URI = 'oauth/v2/token';
    const NECKTIE_USER_PROFILE_URI = 'api/v1/profile';
    const NECKTIE_USER_INVOICES_URI = 'api/v1/invoices';
    const NECKTIE_PRODUCT_ACCESSES_URI = 'api/v1/product-accesses';
    const NECKTIE_BILLING_PLAN_URI = 'api/v1/billing-plan/{id}';
    const NECKTIE_PRODUCT_BILLING_PLANS_URI = 'api/v1/product/{productId}/billing-plans';
    const NECKTIE_PRODUCT_URI = 'api/v1/product/{id}';
    const NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS = 'api/v1/product-access/create-trial/{id}';
    const NECKTIE_GET_PRODUCT_ACCESS = 'api/v1/product-access/{id}';

    const STATE_COOKIE_NAME = 'state';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var NecktieGatewayHelper
     */
    protected $helper;

    /**
     * @var EntityOverrideHandler
     */
    protected $entityOverrideHandler;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Cookie
     */
    protected $stateCookie;

    /**
     * @var NecktieConnector
     */
    protected $connector;

    /**
     * @var string
     */
    protected $necktieUrl;

    /**
     * @var string
     */
    protected $necktieClientId;

    /**
     * @var string
     */
    protected $necktieClientSecret;

    /**
     * @var string
     */
    protected $loginResponseRoute;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        NecktieGatewayHelperInterface $helper,
        NecktieConnector $connector,
        EntityOverrideHandler $entityOverrideHandler,
        LoggerInterface $logger,
        string $necktieUrl = null,
        string $necktieClientId = null,
        string $necktieClientSecret = null,
        string $loginResponseRoute = null
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->helper = $helper;
        $this->connector = $connector;
        $this->entityOverrideHandler = $entityOverrideHandler;
        $this->logger = $logger;

        $this->necktieUrl = $necktieUrl;
        $this->necktieClientId = $necktieClientId;
        $this->necktieClientSecret = $necktieClientSecret;
        $this->loginResponseRoute = $loginResponseRoute;

        $this->setClientBaseUri();
    }

    /**
     * Do not use for redirecting to login form! Redirect to "login_route" route instead!
     *
     * @internal
     *
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function getLoginUrl()
    {
        $necktieUrl = $this->necktieUrl.self::NECKTIE_OAUTH_AUTH_URI;
        $this->stateCookie = $this->createStateCookie();
        $router = $this->router;

        $queryParameters = [
            'client_id' => $this->necktieClientId,
            'client_secret' => $this->necktieClientSecret,
            'redirect_uri' => $this->router->generate(
                $this->loginResponseRoute,
                [],
                $router::ABSOLUTE_URL
            ),
            'grant_type' => 'trusted_authorization',
            'state' => $this->stateCookie->getValue(),
        ];

        $queryParametersString = http_build_query($queryParameters);

        return $necktieUrl.'?'.$queryParametersString;
    }

    /**
     * Call necktie and get User entity.
     * This method should be typically called only in the login process.
     *
     * @param string $accessToken
     * @param bool $createNewUser
     * @param bool $persistNewUser
     *
     * @return UserInterface|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    public function getUserByAccessToken(string $accessToken, bool $createNewUser = false, bool $persistNewUser = true)
    {
        $responseData = json_decode(
            $this->connector->getResponse(
                null,
                'GET',
                self::NECKTIE_USER_PROFILE_URI,
                [],
                $accessToken
            ),
            true
        );

        if (!is_array($responseData)) {
            return;
        }

        $userInfo = $this->helper->getUserInfoFromNecktieProfileResponse($responseData);
        if (is_array($userInfo) && array_key_exists('username', $userInfo)) {
            $user = $this
                ->entityManager
                ->getRepository($this->entityOverrideHandler->getEntityClass(User::class))
                ->findOneBy(
                    ['username' => $userInfo['username']]
                );

            if ($user) {
                return $user;
            } elseif ($createNewUser) {
                return $this->createNewUser($userInfo, $persistNewUser);
            }
        }
    }

    /**
     * Check if a product with given id exists in necktie.
     *
     * @param UserInterface $user
     * @param $necktieId
     *
     * @return bool
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     * @throws UnsuccessfulNecktieResponseException
     */
    public function productExists(UserInterface $user, $necktieId)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = str_replace('{id}', $necktieId, self::NECKTIE_PRODUCT_URI);

        try {
            $response = $this->connector->getResponse($user, 'GET', $necktieUrl);
            if (!$response) {
                return false;
            }
        } catch (ClientException $e) {
            return false;
        } catch (UnsuccessfulNecktieResponseException $e) {
            return false;
        }

        return true;
    }

    /**
     * Update product accesses for given user.
     *
     * @param UserInterface $user
     *
     * @throws UnsuccessfulNecktieResponseException
     *
     * @return \Venice\AppBundle\Entity\ProductAccess[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     */
    public function updateProductAccesses(UserInterface $user)
    {
        $this->refreshAccessTokenIfNeeded($user);
        $givenProductAccesses = [];

        $response = json_decode($this->connector->getResponse($user, 'GET', self::NECKTIE_PRODUCT_ACCESSES_URI), true);

        if (!is_array($response)) {
            return [];
        }

        if (!array_key_exists('productAccesses', $response)) {
            return [];
        }

        if (!is_array($response['productAccesses'])) {
            return [];
        }

        foreach ($response['productAccesses'] as $productAccess) {
            if (is_array($productAccess)
                && array_key_exists('product', $productAccess)
                && array_key_exists('from_date', $productAccess)
            ) {
                $dateTo = null;
                $product = $this
                    ->entityManager
                    ->getRepository(StandardProduct::class)
                    ->findOneBy(
                        ['necktieId' => $productAccess['product']]
                    );

                if (!$product) {
                    continue;
                }

                $dateFrom = \DateTime::createFromFormat(\DateTime::W3C, $productAccess['from_date']);

                if (array_key_exists('to_date', $productAccess)) {
                    $dateTo = \DateTime::createFromFormat(\DateTime::W3C, $productAccess['to_date']);
                }

                if (!($dateFrom instanceof \DateTime)) {
                    continue;
                }

                if (!($dateTo instanceof \DateTime)) {
                    $dateTo = null;
                }

                $givenProductAccesses[] = $user->giveAccessToProduct(
                    $product,
                    $dateFrom,
                    $dateTo,
                    $productAccess['id']
                );
            }
        }

        return $givenProductAccesses;
    }

    /**
     * @param UserInterface $user
     * @param int $productAccessNecktieId
     * @return bool|null|ProductAccess
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getProductAccess(UserInterface $user, int $productAccessNecktieId)
    {
        $necktieUrl = str_replace('{id}', $productAccessNecktieId, self::NECKTIE_GET_PRODUCT_ACCESS);

        $this->refreshAccessTokenIfNeeded($user);

        try {
            $resp = $this->connector->getResponse(
                $user,
                'GET',
                $necktieUrl
            );
            $response = json_decode(
                $resp,
                true
            );
        } catch (UnsuccessfulNecktieResponseException $exception) {
            $this->logger->error($exception->getMessage());

            return null;
        }

        if (!is_array($response) || !array_key_exists('product access', $response)) {
            $this->logger->error(
                'Invalid response from necktie uri' . self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS,
                [$resp, $response]
            );

            return false;
        }

        $accessData = $response['product access'];
        $product = $this
            ->entityManager
            ->getRepository(StandardProduct::class)
            ->findOneBy(['necktieId' => $accessData['product']]);

        $access = new ProductAccess();
        $access->setProduct($product);
        $fromDate = \DateTime::createFromFormat(\DateTime::W3C, $accessData['from_date']);
        $access->setFromDate($fromDate);
        $toDate = \DateTime::createFromFormat(\DateTime::W3C, $accessData['to_date']);
        $access->setToDate($toDate);
        $access->setUser($user);
        $access->setNecktieId($accessData['id']);

        return $access;
    }

    /**
     * @param UserInterface $user
     *
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getInvoices(UserInterface $user)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $response = json_decode(
            $this->connector->getResponse(
                $user,
                'GET',
                self::NECKTIE_USER_INVOICES_URI,
                ['withItems' => true]
            ),
            true
        );

        if (!is_array($response) || !array_key_exists('invoices', $response)) {
            return [];
        }

        return $this->helper->getInvoicesFromNecktieResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewsletters(UserInterface $user)
    {
        //        $this->refreshAccessTokenIfNeeded($user);
//        todo: implement method
    }

    /**
     * @return mixed
     */
    public function getStateCookie()
    {
        return $this->stateCookie;
    }

    /**
     * @param UserInterface $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessToken(UserInterface $user)
    {
        $response = json_decode(
            $this->connector->getResponse(
                $user,
                'POST',
                self::NECKTIE_OATH_TOKEN_URI,
                [
                    'client_id' => $this->necktieClientId,
                    'client_secret' => $this->necktieClientSecret,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $user->getLastRefreshToken(),

                ],
                null,
                true
            ),
            true
        );

        if (!$response) {
            throw new \Exception('Could not get token from response.');
        }

        if (!$this->helper->isRefreshTokenExpiredResponse($response)) {
            $token = $this->helper->createOAuthTokenFromArray($response);

            if ($token) {
                $user->addOAuthToken($token);
                $token->setUser($user);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            } else {
                throw new \Exception('Could not get token from response.');
            }
        } else {
            throw new ExpiredRefreshTokenException('Necktie refresh token has expired.');
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
     * @param UserInterface $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessTokenIfNeeded(UserInterface $user)
    {
        $dateDiff = $user->getLastToken()->getValidTo()->diff(new \DateTime());

        // if the access token is expired or will expire in 10 minutes
        if (!$user->isLastAccessTokenValid() || ($dateDiff->h < 0 && $dateDiff->m < 10)) {
            $this->refreshAccessToken($user);
        }
    }

    /**
     * @param UserInterface $user
     * @param int $billingPlanNecktieId
     *
     * @return bool|int
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function createTrialProductAccess(UserInterface $user, int $billingPlanNecktieId)
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = str_replace('{id}', $billingPlanNecktieId, self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS);


        try {
            $resp = $this->connector->getResponse(
                $user,
                'POST',
                $necktieUrl
            );
            $response = json_decode(
                $resp,
                true
            );
        } catch (UnsuccessfulNecktieResponseException $exception) {
            $this->logger->error($exception->getMessage());

            return false;
        }

        if (!is_array($response) || !array_key_exists('productAccessId', $response)) {
            $this->logger->error(
                'Invalid response from necktie uri' . self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS,
                $response
            );

            return false;
        }

        return $response['productAccessId'];
    }

//    /**
//     * Get billing plan by id
//     *
//     * @param User $user
//     * @param      $id
//     *
//     * @return BillingPlan
//     */
//    public function getBillingPlan(User $user, $id)
//    {
//        $this->refreshAccessTokenIfNeeded($user);
//
//        $necktieUrl = str_replace("{id}", $id, self::NECKTIE_BILLING_PLAN_URI);
//
//        $response = $this->connector->getResponse($user, "GET", $necktieUrl);
//
//        if (is_array($response) && array_key_exists("billing plan", $response)) {
//            return $this->helper->getBillingPlanFromResponse($response["billing plan"]);
//        }
//
//        return null;
//    }

//    /**
//     * Get billing all billing plans for given product.
//     *
//     * @param User $user
//     * @param StandardProduct $product
//     *
//     * @return BillingPlan[]
//     * @throws UnsuccessfulNecktieResponseException
//     */
//    public function getBillingPlans(User $user, StandardProduct $product)
//    {
//        $this->refreshAccessTokenIfNeeded($user);
//
//        $necktieUrl = str_replace("{productId}", $product->getNecktieId(), self::NECKTIE_PRODUCT_BILLING_PLANS_URI);
//        $response = $this->connector->getResponse($user, "GET", $necktieUrl);
//        $billingPlans = [];
//
//        if (is_array($response) && array_key_exists("billing plans", $response)) {
//            foreach ($response["billing plans"] as $billingPlanArray) {
//                $billingPlans[] = $this->helper->getBillingPlanFromResponse($billingPlanArray);
//            }
//
//            return $billingPlans;
//        }
//
//        return null;
//    }

    /**
     * @param $userInfo
     * @param $persist
     *
     * @return UserInterface
     */
    protected function createNewUser($userInfo, $persist)
    {
        $user = $this->entityOverrideHandler->getEntityInstance(User::class);
        $user->setUsername($userInfo['username'])
            ->setEmail($userInfo['email'])
            ->setNecktieId($userInfo['id']);

        if ($persist) {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $user;
    }

    protected function setClientBaseUri()
    {
        // Add slash to the end of the url
        if (substr($this->necktieUrl, -1) !== '/') {
            $this->necktieUrl .= '/';
        }

        $this->connector->setBaseUri($this->necktieUrl);
    }

    protected function createStateCookie()
    {
        return new Cookie(self::STATE_COOKIE_NAME, hash('sha256', mt_rand() + time()));
    }
}
