<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.10.15
 * Time: 15:58
 */

namespace AppBundle\Interfaces;


use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;

interface GatewayInterface
{
    /**
     * Do not use for redirecting to login form! Redirect to "login_route" route instead!
     *
     * @return string
     */
    public function getLoginUrl();


    /**
     * @param User $user
     *
     * @throws UnsuccessfulNecktieResponseException
     *
     * @return ProductAccess[]
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

}