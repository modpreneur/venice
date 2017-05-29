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
use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Venice\AppBundle\Interfaces\NecktieGatewayInterface;

/**
 * Class NecktieGateway
 * @package Venice\AppBundle\Services
 */
class NecktieGateway implements NecktieGatewayInterface
{
    const NECKTIE_OAUTH_AUTH_URI = 'oauth/v2/auth';
    const NECKTIE_OATH_TOKEN_URI = 'oauth/v2/token';
    const NECKTIE_USER_PROFILE_URI = 'api/v1/profile';
    const NECKTIE_USER_ORDERS_URI = 'api/v1/orders';
    const NECKTIE_PRODUCT_ACCESSES_URI = 'api/v1/product-accesses';
    const NECKTIE_BILLING_PLAN_URI = 'api/v1/billing-plan/{id}';
    const NECKTIE_PRODUCT_BILLING_PLANS_URI = 'api/v1/product/{productId}/billing-plans';
    const NECKTIE_PRODUCT_URI = 'api/v1/product/{id}';
    const NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS = 'api/v1/product-access/create-trial/{id}';
    const NECKTIE_GET_PRODUCT_ACCESS = 'api/v1/product-access/{id}';
    const NECKTIE_USER_NEWSLETTER_LIST = 'api/v1/user/newsletters/lists';
    const NECKTIE_USER_NEWSLETTER_LIST_SUBSCRIBE = 'api/v1/user/newsletters/list/{id}';
    const NECKTIE_USER_NEWSLETTER_LIST_UNSUBSCRIBE = 'api/v1/user/newsletters/list/{id}';
    const NECKTIE_NEWSLETTER_LIST = 'api/v1/newsletters/lists';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var NecktieResponseValidator
     */
    protected $responseValidator;

    /**
     * @var NecktieEntityMapper
     */
    protected $entityMapper;

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
     * @var HttpConnector
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
     * @var UserAccessService
     */
    protected $userAccessService;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * NecktieGateway constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param NecktieResponseValidator $validator
     * @param NecktieEntityMapper $mapper
     * @param HttpConnector $connector
     * @param EntityOverrideHandler $entityOverrideHandler
     * @param LoggerInterface $logger
     * @param UserAccessService $accessService
     * @param string $necktieUrl
     * @param string $necktieClientId
     * @param string $necktieClientSecret
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        NecktieResponseValidator $validator,
        NecktieEntityMapper $mapper,
        HttpConnector $connector,
        EntityOverrideHandler $entityOverrideHandler,
        LoggerInterface $logger,
        UserAccessService $accessService,
        string $necktieUrl,
        string $necktieClientId,
        string $necktieClientSecret
    ) {
        $this->entityManager = $entityManager;
        $this->responseValidator = $validator;
        $this->connector = $connector;
        $this->entityOverrideHandler = $entityOverrideHandler;
        $this->logger = $logger;
        $this->entityMapper = $mapper;
        $this->userAccessService = $accessService;
        $this->necktieUrl = $necktieUrl;
        $this->necktieClientId = $necktieClientId;
        $this->necktieClientSecret = $necktieClientSecret;

        $this->setClientBaseUri();
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
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws UnsuccessfulNecktieResponseException
     * @throws \Exception
     */
    public function getUserByAccessToken(
        string $accessToken,
        bool $createNewUser = false,
        bool $persistNewUser = true
    ): ?User {
        $responseData = \json_decode(
            $this->connector->getResponse(
                null,
                'GET',
                self::NECKTIE_USER_PROFILE_URI,
                [],
                $accessToken
            ),
            true
        );
        if (!\is_array($responseData)) {
            return null;
        }
        $userInfo = $this->entityMapper->getUserInfoFromNecktieProfileResponse($responseData);

        if (\array_key_exists('username', $userInfo)) {
            /** @var User $user */
            $user = $this
                ->entityManager
                ->getRepository($this->entityOverrideHandler->getEntityClass(User::class))
                ->findOneBy(
                    ['username' => $userInfo['username']]
                );
            if ($user) {
                return $user;
            } elseif ($createNewUser === true) {
                return $this->createNewUser($userInfo, $persistNewUser);
            }
        }

        return null;
    }


    /**
     * Check if a product with given id exists in necktie.
     *
     * @param User $user
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
    public function productExists(User $user, $necktieId): bool
    {
        $this->refreshAccessTokenIfNeeded($user);
        $necktieUrl = \str_replace('{id}', $necktieId, self::NECKTIE_PRODUCT_URI);

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
     * @param User $user
     *
     * @throws UnsuccessfulNecktieResponseException
     *
     * @return \Venice\AppBundle\Entity\ProductAccess[] All user's product accesses.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     */
    public function updateProductAccesses(User $user): array
    {
        $this->refreshAccessTokenIfNeeded($user);
        $givenProductAccesses = [];
        $response = \json_decode(
            $this
                ->connector
                ->getResponse($user, 'GET', self::NECKTIE_PRODUCT_ACCESSES_URI),
            true
        );

        foreach ($this->entityMapper->getProductAccesses($response) as $access) {
            //note that the $access variable does not contain an instance which should get outsde of the method
            //it serves only as a container for the data

            $givenProductAccesses[] = $this->userAccessService->giveAccessToProduct(
                $user,
                $access->getProduct(),
                $access->getFromDate(),
                $access->getToDate(),
                $access->getNecktieId()
            );
        }

        return $givenProductAccesses;
    }


    /**
     * @param User $user
     * @param int $productAccessNecktieId
     *
     * @return ProductAccess|null
     *
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getProductAccess(User $user, int $productAccessNecktieId): ?ProductAccess
    {
        $necktieUrl = \str_replace('{id}', $productAccessNecktieId, self::NECKTIE_GET_PRODUCT_ACCESS);
        $this->refreshAccessTokenIfNeeded($user);

        try {
            $resp = $this->connector->getResponse(
                $user,
                'GET',
                $necktieUrl
            );
            $response = \json_decode(
                $resp,
                true
            );
        } catch (UnsuccessfulNecktieResponseException $exception) {
            $this->logger->error($exception->getMessage());
            return null;
        }

        if (!\is_array($response) || !\array_key_exists('product access', $response)) {
            $this->logger->error(
                'Invalid response from necktie uri' . self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS,
                [$resp, $response]
            );
            return null;
        }


        $access = $this->entityMapper->getProductAccess($response);

        if ($access !== null) {
            $access->setUser($user);
        }

        return $access;
    }


    /**
     * @param User $user
     *
     * @return Order[]
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getOrders(User $user): array
    {
        $this->refreshAccessTokenIfNeeded($user);
        $resp = $this->connector->getResponse(
            $user,
            'GET',
            self::NECKTIE_USER_ORDERS_URI,
            ['withItems' => true]
        );

        $response = \json_decode(
            $resp,
            true
        );

        return $this->entityMapper->getOrders($response);
    }


    /**
     * @param User $user
     * @param array $newsletters
     *
     * @return array
     *
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \RuntimeException
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateNewsletters(User $user, array $newsletters = []): array
    {
        $this->refreshAccessTokenIfNeeded($user);
        $stat = [];

        try {
            foreach ($newsletters as $newsletter) {
                if ($newsletter['isSubscribed'] === true) {
                    $url = \str_replace(
                        '{id}',
                        $newsletter['listId'],
                        self::NECKTIE_USER_NEWSLETTER_LIST_SUBSCRIBE
                    );

                    $stat[] = \json_decode($this->connector->getResponse(
                        $user,
                        'POST',
                        $url
                    ), true);
                } else {
                    $url = \str_replace(
                        '{id}',
                        $newsletter['listId'],
                        self::NECKTIE_USER_NEWSLETTER_LIST_UNSUBSCRIBE
                    );

                    $stat[] = \json_decode($this->connector->getResponse(
                        $user,
                        'DELETE',
                        $url
                    ), true);
                }
            }

            return $stat;

        } catch (\Exception $exception) {
            return [$exception->getMessage()];
        }
    }


    /**
     * @param User $user
     *
     * @return array
     *
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     */
    public function getNewsletters(User $user): array
    {
        $this->refreshAccessTokenIfNeeded($user);

        try {
            $allNewslettersResponse = $this->connector->getResponse(
                $user,
                'GET',
                self::NECKTIE_NEWSLETTER_LIST
            );

            $userNewslettersResponse = $this->connector->getResponse(
                $user,
                'GET',
                self::NECKTIE_USER_NEWSLETTER_LIST
            );



        } catch (UnsuccessfulNecktieResponseException $exception) {
            $this->logger->error($exception->getMessage());

            return [];
        }

        $newsletters = \json_decode(
            $allNewslettersResponse,
            true
        );

        $userNewsletters = \json_decode(
            $userNewslettersResponse,
            true
        )['data'];

        $lists = [];

        // todo: needs refactoring
        foreach ($newsletters as $name => $items) {
            foreach ($items as $key => $value) {
                $isSubscribed = false;

                if (isset($userNewsletters[$name])) {
                    foreach ($userNewsletters[$name] as $id) {
                        if ($id === $key) {
                            $isSubscribed = true;
                        }
                    }
                }

                $lists[] = [
                    'listId' => $key,
                    'title' => $value,
                    'isSubscribed' => $isSubscribed
                ];
            }
        }

        return $lists;
    }


    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessToken(User $user): void
    {
        $response = \json_decode(
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

        if (!$this->responseValidator->isRefreshTokenExpiredResponse($response)) {
            $token = $this->entityMapper->createOAuthTokenFromArray($response);
            if ($token !== null) {
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
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessTokenIfNeeded(User $user): void
    {
        if ($user->getLastToken() !== null) {
            $dateDiff = $user->getLastToken()->getValidTo()->diff(new \DateTime());
            // if the access token is expired or will expire in 10 minutes
            // @note: m = months, i = minutes. Really English(and PHP)?
            if (($dateDiff->h < 1 && $dateDiff->i < 10) || !$user->isLastAccessTokenValid()) {
                $this->refreshAccessToken($user);
            }
        } else {
            $this->refreshAccessToken($user);
        }
    }


    /**
     * @param User $user
     * @param int $billingPlanNecktieId
     *
     * @return ProductAccess|null
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function createTrialProductAccess(User $user, int $billingPlanNecktieId): ?ProductAccess
    {
        $this->refreshAccessTokenIfNeeded($user);

        $necktieUrl = \str_replace(
            '{id}',
            $billingPlanNecktieId,
            self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS
        );

        try {
            $resp = $this->connector->getResponse(
                $user,
                'POST',
                $necktieUrl
            );
            
            $response = \json_decode(
                $resp,
                true
            );
            
             $response = $response ?? [];
        } catch (UnsuccessfulNecktieResponseException $exception) {
            $this->logger->error($exception->getMessage());
            
            return null;
        }
        if (!\is_array($response) || !\array_key_exists('productAccessId', $response)) {
            $this->logger->error(
                'Invalid response from necktie uri' . self::NECKTIE_CREATE_TRIAL_PRODUCT_ACCESS,
                $response
            );
            
            return null;
        }

        return $this->getProductAccess($user, $response['productAccessId']);
    }

    /**
     * @param $userInfo
     * @param $persist
     *
     * @return User
     */
    protected function createNewUser($userInfo, $persist): User
    {
        /** @var User $user */
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


    /**
     * Set url to the connector
     */
    protected function setClientBaseUri(): void
    {
        $this->connector->setBaseUri($this->necktieUrl);
    }
}
