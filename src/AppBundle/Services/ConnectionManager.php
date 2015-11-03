<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:07
 */

namespace AppBundle\Services;


use AppBundle\Entity\Invoice;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use AppBundle\Exceptions\ExpiredRefreshTokenException;
use AppBundle\Interfaces\ConnectionManagerInterface;
use AppBundle\Interfaces\GatewayInterface;
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
     * @return bool
     */
    protected function hasSecondaryGateway()
    {
        return !($this->secondaryGateway == null);
    }
}