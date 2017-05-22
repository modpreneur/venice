<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:29.
 */

namespace Venice\AppBundle\Entity\Product;


/**
 * Class FreeProduct.
 */
class FreeProduct extends Product
{
    const TYPE = 'free';

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }
}
