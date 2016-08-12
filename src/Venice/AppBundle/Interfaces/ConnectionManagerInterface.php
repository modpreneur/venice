<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 19:56
 */

namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Invoice;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;

interface ConnectionManagerInterface
{
    /**
     * Get url to login page.
     * This url typically links to another website(e.g. necktie).
     *
     * @return string
     */
    public function getLoginUrl();


    /**
     * Update product accesses for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException Should be catched in a event listener listening kernel.exception.
     *
     * @return ProductAccess[]
     */
    public function updateProductAccesses(User $user);


    /**
     * Get invoices for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException Should be catched in a event listener listening kernel.exception.
     *
     * @return Invoice[]
     */
    public function getInvoices(User $user);


    /**
     * Get billing plan by id.
     *
     * @param User $user
     * @param      $id
     *
     * @return BillingPlan
     */
    public function getBillingPlan(User $user, $id);


    /**
     * Get all billing plans for given product.
     *
     * @param User            $user
     *
     * @param StandardProduct $product
     *
     * @return \Venice\AppBundle\Entity\BillingPlan[]
     */
    public function getBillingPlans(User $user, StandardProduct $product);
}
