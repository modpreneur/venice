<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13
 */

namespace Venice\AppBundle\Entity;

use Trinity\Component\Core\Interfaces\ProductInterface;
use Trinity\Component\EntityCore\Entity\BaseBillingPlan;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Trinity\NotificationBundle\Annotations as N;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class BillingPlan
 */
class BillingPlan extends BaseBillingPlan implements NotificationEntityInterface
{
    /**
     * @var int
     */
    protected $necktieId;


    /**
     * @var StandardProduct
     */
    protected $product;


    /**
     * String which can be used for displaying the price of this billing plan
     *
     * @var string
     */
    protected $price;


    public function __construct()
    {
        $this->initialPrice = 0;
        $this->rebillPrice = 0;
        $this->frequency = 0;
        $this->rebillTimes = 0;
        $this->price = '';
        $this->updateTimestamps();
    }



    /**
     * @return int
     */
    public function getNecktieId()
    {
        return $this->necktieId;
    }


    /**
     * @param int $necktieId
     *
     * @return BillingPlan
     */
    public function setNecktieId($necktieId)
    {
        $this->necktieId = $necktieId;

        return $this;
    }


    /**
     * @N\AssociationGetter()
     *
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\Product\StandardProduct")
     *
     * @param StandardProduct $product
     *
     * @return BillingPlan
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }


    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }


    /**
     * @param string $price
     *
     * @return BillingPlan
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }


    /** @return ClientInterface[] */
    public function getClients()
    {
       return [];
    }
}