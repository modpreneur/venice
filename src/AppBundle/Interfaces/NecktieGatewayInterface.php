<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:35
 */

namespace AppBundle\Interfaces;


use AppBundle\Entity\User;
use AppBundle\Exceptions\ExpiredRefreshTokenException;
use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Symfony\Component\HttpFoundation\Cookie;

interface NecktieGatewayInterface extends GatewayInterface
{
    /**
     * Call necktie and get User entity.
     * This method should be typically called only in the login process.
     *
     *
     * @param string     $accessToken
     * @param bool|false $createNewUser
     * @param bool|true  $persistNewUser
     *
     * @return User|null
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getUserByAccessToken($accessToken = null, $createNewUser = false, $persistNewUser = true);


    /**
     * @return Cookie
     */
    public function getStateCookie();


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

}