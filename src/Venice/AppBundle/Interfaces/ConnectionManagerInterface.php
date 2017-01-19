<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 19:56.
 */
namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Interfaces\InvoiceInterface;
use Venice\AppBundle\Entity\Interfaces\StandardProductInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Entity\ProductAccess;
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
     * @param UserInterface $user
     *
     * @throws ExpiredRefreshTokenException Should be catched in a event listener listening kernel.exception.
     *
     * @return ProductAccess[]
     */
    public function updateProductAccesses(UserInterface $user);

    /**
     * Get invoices for given user.
     *
     * @param UserInterface $user
     *
     * @throws ExpiredRefreshTokenException Should be catched in a event listener listening kernel.exception.
     *
     * @return InvoiceInterface[]
     */
    public function getInvoices(UserInterface $user);

    /**
     * Get billing plan by id.
     *
     * @param UserInterface $user
     * @param      $id
     *
     * @return BillingPlan
     */
    public function getBillingPlan(UserInterface $user, $id);

    /**
     * Get all billing plans for given product.
     *
     * @param UserInterface $user
     * @param StandardProductInterface $product
     *
     * @return \Venice\AppBundle\Entity\BillingPlan[]
     */
    public function getBillingPlans(UserInterface $user, StandardProductInterface $product);
}
