<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 13:13
 */

namespace AppBundle\Entity;

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


    public function __construct()
    {
        $this->initialPrice = 0;
        $this->rebillPrice = 0;
        $this->frequency = 0;
        $this->rebillTimes = 0;
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
    public function getRebilTimes()
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
}