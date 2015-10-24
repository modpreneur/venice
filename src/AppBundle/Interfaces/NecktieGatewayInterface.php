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

interface NecktieGatewayInterface
{

    /**
     * Do not use for redirecting to login form! Redirect to "login_route" route instead!
     *
     * @return string
     */
    public function getRedirectUrlToNecktieLogin();


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
     * @param User $user
     *
     * @throws UnsuccessfulNecktieResponseException
     */
    public function updateProductAccesses(User $user);


    /**
     * @param User $user
     *
     * @return array
     * @throws UnsuccessfulNecktieResponseException
     */
    public function getInvoices(User $user);


    /** todo
     * @param User $user
     *
     * @return mixed
     */
    public function getNewsletters(User $user);

    /**
     * @return mixed
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