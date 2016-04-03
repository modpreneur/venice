<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13
 */

namespace Venice\AppBundle\Entity;

use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity()
 * @HasLifecycleCallbacks
 * @ORM\Table(name="billing_plan")
 *
 * @UniqueEntity("necktieId")
 *
 * Class BillingPlan
 */
class BillingPlan
{
    use Timestampable;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;


    /**
     * @var int
     *
     * @ORM\Column(name="necktie_id", type="integer", nullable=false, unique=true)
     */
    protected $necktieId;


    /**
     * Price which will be paid at the start.
     *
     * @var float
     *
     * @ORM\Column(name="initial_price", type="float", nullable=false)
     */
    protected $initialPrice;


    /**
     * Price which will be paid periodically in case of recurring.
     *
     * @var float
     *
     * @ORM\Column(name="rebill_price", type="float", nullable=true)
     */
    protected $rebillPrice;


    /**
     * Time between payments(rebillPrice).
     *
     * @var int
     *
     * @ORM\Column(name="frequency", type="integer", nullable=true)
     */
    protected $frequency;


    /**
     * Count of all payments for this product(including initial price)
     *
     * @var int
     *
     * @ORM\Column(name="rebill_times", type="integer", nullable=true)
     */
    protected $rebillTimes;


    /**
     * @var StandardProduct
     *
     * @ORM\ManyToOne(targetEntity="Venice\AppBundle\Entity\Product\StandardProduct", inversedBy="billingPlans", cascade={"PERSIST"})
     */
    protected $product;


    /**
     * String which can be used for displaying the price of this billing plan
     *
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=50)
     */
    protected $price;


    public function __construct()
    {
        $this->initialPrice = 0;
        $this->rebillPrice = 0;
        $this->frequency = 0;
        $this->rebillTimes = 0;
        $this->price = "";
        $this->updateTimestamps();
    }


    /**
     * Set current billing plan as default
     */
    public function setAsDefault()
    {
        $this->product->setDefaultBillingPlan($this);
    }

    /**
     * @param int $id
     *
     * @return BillingPlan
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return float
     */
    public function getInitialPrice()
    {
        return $this->initialPrice;
    }


    /**
     * @param float $initialPrice
     *
     * @return BillingPlan
     */
    public function setInitialPrice($initialPrice)
    {
        $this->initialPrice = $initialPrice;

        return $this;
    }


    /**
     * @return float
     */
    public function getRebillPrice()
    {
        return $this->rebillPrice;
    }


    /**
     * @param float $rebillPrice
     *
     * @return BillingPlan
     */
    public function setRebillPrice($rebillPrice)
    {
        $this->rebillPrice = $rebillPrice;

        return $this;
    }


    /**
     * @return int
     */
    public function getFrequency()
    {
        return $this->frequency;
    }


    /**
     * @param int $frequency
     *
     * @return BillingPlan
     */
    public function setFrequency($frequency)
    {
        $this->frequency = $frequency;

        return $this;
    }


    /**
     * @return int
     */
    public function getRebillTimes()
    {
        return $this->rebillTimes;
    }


    /**
     * @param int $rebilTimes
     *
     * @return BillingPlan
     */
    public function setRebillTimes($rebilTimes)
    {
        $this->rebillTimes = $rebilTimes;

        return $this;
    }


    /**
     * @return bool
     */
    public function isRecurring()
    {
        return $this->frequency === 0;

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
     * @param int $amemberId
     *
     * @return BillingPlan
     */
    public function setAmemberId($amemberId)
    {
        $this->amemberId = $amemberId;

        return $this;
    }


    /**
     * @return StandardProduct
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * @param StandardProduct $product
     *
     * @return BillingPlan
     */
    public function setProduct(StandardProduct $product)
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


    /**
     * Get type of the billing plan - check rebillTimes.
     *
     * @return bool
     */
    public function getType()
    {
        return ($this->rebillTimes == 0) ? "standard" : "recurring";
    }
}