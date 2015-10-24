<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 10.10.15
 * Time: 10:21
 */

namespace AppBundle\Services;


use AppBundle\Entity\Product\FreeProduct;
use AppBundle\Entity\Product\Product;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use AppBundle\Interfaces\ProductAccessManagerInterface;
use Doctrine\ORM\EntityManager;

class ProductAccessManager implements ProductAccessManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param User    $user
     * @param Product $product
     *
     * @return boolean
     */
    public function hasAccessToProduct(User $user, Product $product)
    {
        if($product instanceof FreeProduct)
        {
            return true;
        }

        $productAccess = $this
            ->entityManager
            ->getRepository("AppBundle:ProductAccess")
            ->findOneBy(
                ["user" => $user, "product" => $product]
            );

        if($productAccess instanceof ProductAccess)
        {
            return true;
        }

        return false;
    }


    /**
     * @param User           $user
     * @param Product        $product
     * @param \DateTime      $dateFrom Starting datetime
     * @param \DateTime|null $dateTo   Ending datetime or null if it is lifetime access
     *
     * @return void
     */
    public function giveAccessToProduct(User $user, Product $product, \DateTime $dateFrom, \DateTime $dateTo = null)
    {
        //$productAccess = $this
        //    ->entityManager
        //    ->getRepository("AppBundle:ProductAccess")
        //    ->findOneBy(
        //        ["user" => $user, "product" => $product]
        //    );

        if(!$this->hasAccessToProduct($user, $product))
        {
            $productAccess = new ProductAccess();
            $productAccess
                ->setProduct($product)
                ->setUser($user)
                ->setDateFrom($dateFrom)
                ->setDateTo($dateTo);

            $this->entityManager->persist($productAccess);
            $this->entityManager->flush();
        }
    }
}