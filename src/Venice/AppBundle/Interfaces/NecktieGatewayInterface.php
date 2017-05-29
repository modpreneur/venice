<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:35.
 */
namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;

/**
 * Interface NecktieGatewayInterface
 * @package Venice\AppBundle\Interfaces
 */
interface NecktieGatewayInterface
{
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
    ): ?User;

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
    public function productExists(User $user, $necktieId): bool;

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
    public function updateProductAccesses(User $user): array;

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
    public function getProductAccess(User $user, int $productAccessNecktieId): ?ProductAccess;

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
    public function getOrders(User $user): array;

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
    public function updateNewsletters(User $user, array $newsletters = []): array;

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
    public function getNewsletters(User $user): array;

    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessToken(User $user): void;

    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refreshAccessTokenIfNeeded(User $user): void;

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
    public function createTrialProductAccess(User $user, int $billingPlanNecktieId): ?ProductAccess;
}
