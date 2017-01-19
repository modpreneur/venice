<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:29.
 */
namespace Venice\AppBundle\Entity\Product;

use Venice\AppBundle\Entity\Interfaces\FreeProductInterface;

/**
 * Class FreeProduct.
 */
class FreeProduct extends Product implements FreeProductInterface
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
