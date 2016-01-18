<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 14:53
 */

namespace AppBundle\Event;


use AppBundle\Entity\Product\FreeProduct;
use Symfony\Component\EventDispatcher\Event;

class FreeProductCreatedEvent extends Event
{
    /** @var FreeProduct */
    protected $product;

    /**
     * FreeProductCreatedEvent constructor.
     * @param FreeProduct $product
     */
    public function __construct(FreeProduct $product)
    {
        $this->product= $product;
    }

    /**
     * @return FreeProduct
     */
    public function getProduct()
    {
        return $this->product;
    }
}