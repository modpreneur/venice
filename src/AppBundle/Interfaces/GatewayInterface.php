<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.10.15
 * Time: 15:58
 */

namespace AppBundle\Interfaces;


use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;

interface GatewayInterface
{
    /**
     * @return string
     */
    public function getLoginUrl();


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
    public function getInvoices(User $user);


    /** todo
     * @param User $user
     *
     * @return mixed
     */
    public function getNewsletters(User $user);


    /**
     * Get billing plan by id
     *
     * @param User $user
     * @param      $id
     *
     * @return BillingPlan
     */
    public function getBillingPlan(User $user, $id);


    /**
     * Get all billing plans.
     *
     * @param User            $user
     *
     * @param StandardProduct $product
     *
     * @return \AppBundle\Entity\BillingPlan[]
     */
    public function getBillingPlans(User $user, StandardProduct $product);
}