<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 19:56
 */

namespace AppBundle\Interfaces;


use AppBundle\Entity\User;
use AppBundle\Exceptions\ExpiredRefreshTokenException;

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
     * @return null
     */
    public function updateProductAccesses(User $user);


    /**
     * Get invoices for given user.
     *
     * @param User $user
     *
     * @throws ExpiredRefreshTokenException Should be catched in a event listener listening kernel.exception.
     *
     * @return \AppBundle\Entity\Invoice[]
     */
    public function getInvoices(User $user);
}