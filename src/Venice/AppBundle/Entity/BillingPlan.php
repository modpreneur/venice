<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13.
 */

namespace Venice\AppBundle\Entity;

use JMS\Serializer\Annotation\SerializedName;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BaseBillingPlan;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use JMS\Serializer\Annotation as Serializer;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class BillingPlan.
 */
class BillingPlan extends BaseBillingPlan implements NotificationEntityInterface
{
    /**
     * @var int
     * @SerializedName("id")
     */
    protected $necktieId;

    /**
     * @var StandardProduct
     * @Serializer\Exclude()
     */
    protected $product;

    /**
     * @var PaySystemVendor paySystemVendor
     * @Serializer\Exclude()
     */
    protected $paySystemVendor;

    /**
     * String which can be used for displaying the price of this billing plan.
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
     * @return StandardProduct
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
    public function setProduct($product): void
    {
        $this->product = $product;
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

    /**
     * @N\AssociationGetter()
     *
     * @return PaySystemVendor
     */
    public function getPaySystemVendor()
    {
        return $this->paySystemVendor;
    }

    /**
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\PaySystemVendor")
     *
     * @param PaySystemVendor $paySystemVendor
     */
    public function setPaySystemVendor($paySystemVendor)
    {
        $this->paySystemVendor = $paySystemVendor;
    }

    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }
}
