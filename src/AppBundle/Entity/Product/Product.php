<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:44
 */

namespace AppBundle\Entity\Product;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class Product
 *
 * @ORM\Entity()
 * @ORM\Table(name="product")
 *
 * @package AppBundle\Entity\Product
 */
class Product extends BaseProduct
{
    /**
     * @var
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;


    /**
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    protected $orderNumber;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="product")
     */
    protected $contentProducts;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="product", cascade={"remove"})
     */
    protected $productAccesses;


    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param mixed $enabled
     *
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }


    /**
     * @param int $orderNumber
     *
     * @return Product
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

}