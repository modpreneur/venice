<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContentProduct
 *
 * @ORM\Entity()
 * @ORM\Table(name="contents_products")
 *
 * @package AppBundle\Entity
 */
class ContentProduct
{
    /**
     * @var
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\Content", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;


    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;


    /**
     * @var
     *
     * @ORM\Column(name="delay", type="integer", nullable=false)
     */
    protected $delay;


    /**
     * @var
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     */
    protected $orderNumber;


    public function __construct()
    {
        $this->content = null;
        $this->product = null;
        $this->delay = 0;
        $this->orderNumber = 0;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @param mixed $content
     *
     * @return ContentProduct
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * @param mixed $product
     *
     * @return ContentProduct
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->delay;
    }


    /**
     * @param mixed $delay
     *
     * @return ContentProduct
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }


    /**
     * @param mixed $orderNumber
     *
     * @return ContentProduct
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }
}