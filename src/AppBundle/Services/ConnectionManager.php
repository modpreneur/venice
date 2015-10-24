<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:07
 */

namespace AppBundle\Services;


use AppBundle\Entity\User;
use AppBundle\Exceptions\ExpiredRefreshTokenException;
use AppBundle\Interfaces\ConnectionManagerInterface;
use AppBundle\Interfaces\NecktieGatewayInterface;

class ConnectionManager implements ConnectionManagerInterface
{
    protected $necktieGateway;


    /**
     * @param NecktieGatewayInterface $necktieGateway
     */
    public function __construct(NecktieGatewayInterface $necktieGateway)
    {
        $this->necktieGateway = $necktieGateway;
    }


    /**
     * Get url to login page.
     * This url typically links to another website(e.g. necktie).
     *
     * @return string
     */
    public function getLoginUrl()
    {
        //return router->generate("login_route");
    }


    /**
     * Update product accesses for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     *
     * @return null
     */
    public function updateProductAccesses(User $user)
    {
        // TODO: Implement updateProductAccesses() method.
    }


    /**
     * Get invoices for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException
     *
     * @return \AppBundle\Entity\Invoice[]
     */
    public function getInvoices(User $user)
    {
        // TODO: Implement getInvoices() method.
    }
}