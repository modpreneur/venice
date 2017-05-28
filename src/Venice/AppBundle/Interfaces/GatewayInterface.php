<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.10.15
 * Time: 15:58.
 */
namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;

interface GatewayInterface
{
//    /**
//     * @return string
//     */
//    public function getLoginUrl();

    /**
     * @param User $user
     *
     * @return ProductAccess[]
     */
    public function updateProductAccesses(User $user);

    /**
     * @param User $user
     *
     * @return array
     */
    public function getOrders(User $user);

    /** todo
     * @param User $user
     *
     * @return mixed
     */
    public function getNewsletters(User $user);
}
