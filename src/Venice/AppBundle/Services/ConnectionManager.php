<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:07
 */

namespace Venice\AppBundle\Services;


use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Invoice;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Venice\AppBundle\Interfaces\ConnectionManagerInterface;
use Venice\AppBundle\Interfaces\GatewayInterface;
use Venice\AppBundle\Interfaces\NecktieGatewayInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConnectionManager implements ConnectionManagerInterface
{
    /**
     * @var GatewayInterface
     */
    protected $primaryGateway;

    /**
     * @var GatewayInterface
     */
    protected $secondaryGateway;


    /**
     * @param ContainerInterface $container
     * @param string             $primaryGatewayService
     * @param string             $secondaryGatewayService
     */
    public function __construct(ContainerInterface $container, $primaryGatewayService = "", $secondaryGatewayService = "")
    {
        $this->primaryGateway = $container->get($primaryGatewayService);
        $this->secondaryGateway = $container->get($secondaryGatewayService, $container::NULL_ON_INVALID_REFERENCE);
    }


    /**
     * Get url to login page.
     * This url typically links to another website(e.g. necktie).
     *
     * @return string
     */
    public function getLoginUrl()
    {
        $this->primaryGateway->getLoginUrl();
    }


    /**
     * Update product accesses for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     *
     * @return ProductAccess[]
     */
    public function updateProductAccesses(User $user)
    {
        return $this->primaryGateway->updateProductAccesses($user);
    }


    /**
     * Get invoices for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     *
     * @return Invoice[]
     */
    public function getInvoices(User $user)
    {
        return $this->primaryGateway->getInvoices($user);
    }


    /**
     * Get billing plan by id
     *
     * @param User $user
     * @param      $id
     *
     * @return BillingPlan
     */
    public function getBillingPlan(User $user, $id)
    {
        return $this->primaryGateway->getBillingPlan($user, $id);
    }


    /**
     * @return bool
     */
    protected function hasSecondaryGateway()
    {
        return !($this->secondaryGateway == null);
    }


    /**
     * Get all billing plans for given product.
     *
     * @param User            $user
     *
     * @param StandardProduct $product
     *
     * @return \Venice\AppBundle\Entity\BillingPlan[]
     */
    public function getBillingPlans(User $user, StandardProduct $product)
    {
        return $this->primaryGateway->getBillingPlans($user, $product);


        //try
        //{
        //}
        //catch(UnsuccessfulNecktieResponseException $e)
        //{
        //    /** @var NecktieGatewayInterface $necktieGateway */
        //    $necktieGateway = $this->primaryGateway;
        //    $necktieGateway->refreshAccessToken($user);
        //
        //    return $this->primaryGateway->getBillingPlans($user, $product);
        //}
    }
}