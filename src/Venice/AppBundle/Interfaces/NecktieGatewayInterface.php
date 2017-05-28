<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:35.
 */
namespace Venice\AppBundle\Interfaces;

use Symfony\Component\HttpFoundation\Cookie;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;

interface NecktieGatewayInterface extends GatewayInterface
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
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getUserByAccessToken(string $accessToken, bool $createNewUser = false, bool $persistNewUser = true);

//    /**
//     * @return Cookie
//     */
//    public function getStateCookie();

    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     */
    public function refreshAccessToken(User $user);

    /**
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     */
    public function refreshAccessTokenIfNeeded(User $user);

    /**
     * @param User $user
     * @param int $billingPlanNecktieId
     *
     * @return bool|int
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function createTrialProductAccess(User $user, int $billingPlanNecktieId);

    /**
     * @param User $user
     * @param int $productAccessNecktieId
     * @return bool|null|ProductAccess
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function getProductAccess(User $user, int $productAccessNecktieId);

}
