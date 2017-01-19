<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.10.15
 * Time: 15:58.
 */
namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Entity\ProductAccess;

interface GatewayInterface
{
    /**
     * @return string
     */
    public function getLoginUrl();

    /**
     * @param UserInterface $user
     *
     * @return ProductAccess[]
     */
    public function updateProductAccesses(UserInterface $user);

    /**
     * @param UserInterface $user
     *
     * @return array
     */
    public function getInvoices(UserInterface $user);

    /** todo
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function getNewsletters(UserInterface $user);

//    /**
//     * Get billing plan by id
//     *
//     * @param User $user
//     * @param      $id
//     *
//     * @return BillingPlan
//     */
//    public function getBillingPlan(User $user, $id);
//
//
//    /**
//     * Get all billing plans.
//     *
//     * @param User            $user
//     *
//     * @param StandardProduct $product
//     *
//     * @return \Venice\AppBundle\Entity\BillingPlan[]
//     */
//    public function getBillingPlans(User $user, StandardProduct $product);
}
