<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:29
 */

namespace AppBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="product_free")
 *
 * Class FreeProduct
 * @package AppBundle\Entity\Product
 */
class FreeProduct extends Product
{
    const TYPE = "free";

    public function getType()
    {
        return self::TYPE;
    }
}