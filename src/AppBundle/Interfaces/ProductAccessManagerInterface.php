<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 10.10.15
 * Time: 10:26
 */

namespace AppBundle\Interfaces;


use AppBundle\Entity\Product\Product;
use AppBundle\Entity\User;

interface ProductAccessManagerInterface
{

    /**
     * @param User           $user
     * @param Product        $product
     *
     * @return boolean
     */
    public function hasAccessToProduct(User $user, Product $product);


    /**
     * @param User           $user
     * @param Product        $product
     * @param \DateTime      $dateFrom  Starting datetime
     * @param \DateTime|null $dateTo    Ending datetime or null if it is lifetime access
     *
     * @return boolean
     */
    public function giveAccessToProduct(User $user, Product $product, \DateTime $dateFrom, \DateTime $dateTo = null);

}