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
     * @ORM\Column(name="id)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;

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
    protected $rebilTimes;


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
        return $this->rebilTimes;
    }


    /**
     * @param int $rebilTimes
     *
     * @return BillingPlan
     */
    public function setRebilTimes($rebilTimes)
    {
        $this->rebilTimes = $rebilTimes;

        return $this;
    }


    /**
     * @return bool
     */
    public function isRecurring()
    {
        return $this->frequency === 0;

    }
}