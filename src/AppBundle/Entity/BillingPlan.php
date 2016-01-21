<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Product\StandardProduct;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="billing_plan")
 *
 * Class BillingPlan
 * @package AppBundle\Entity
 */
class BillingPlan
{
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
     * @ORM\Column(name="necktie_id", type="integer", nullable=true, unique=true)
     */
    protected $necktieId;


    /**
     * @var int
     *
     * @ORM\Column(name="amember_id", type="integer", nullable=true, unique=true)
     */
    protected $amemberId;


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
     * @ORM\Column(name="rebill_price", type="float", nullable=false)
     */
    protected $rebillPrice;


    /**
     * Time between payments(rebillPrice).
     *
     * @var int
     *
     * @ORM\Column(name="frequency", type="integer", nullable=false)
     */
    protected $frequency;


    /**
     * Count of all payments for this product(including initial price)
     *
     * @var int
     *
     * @ORM\Column(name="rebill_times", type="integer", nullable=false)
     */
    protected $rebillTimes;


    /**
     * @var StandardProduct
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Product\StandardProduct", cascade={"PERSIST", "REFRESH"}, mappedBy="billingPlan")
     */
    protected $product;


    /**
     * @var
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
     * @return int
     */
    public function getAmemberId()
    {
        return $this->amemberId;
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
     * Generate and set the price string.
     *
     * @return string price
     */
    public function generateAndSetPriceString()
    {
        $fullPrice = sprintf("$%.2f", $this->initialPrice);

        if ($this->getType() === 'recurring') {
            $fullPrice = $fullPrice.' and ';

            if ($this->rebillTimes != 999) {
                $fullPrice = $fullPrice.($this->rebillTimes - 1).' times ';
            }

            $fullPrice = $fullPrice.sprintf("$%.2f", $this->rebillPrice);

            if ($this->rebillTimes == 999) {
                $fullPrice = $fullPrice.' lifetime';
            }
        }

        $this->price = $fullPrice;

        return $fullPrice;
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