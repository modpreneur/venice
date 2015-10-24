<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32
 */

namespace AppBundle\Entity\Product;


use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity()
 * @ORM\Table(name="product_standard")
 *
 * Class StandardProduct
 * @package AppBundle\Entity\Product
 */
class StandardProduct extends Product
{

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


    public function __construct()
    {
        parent::__construct();

        $this->initialPrice = 0;
        $this->rebillPrice = 0;
        $this->frequency = 0;
        $this->rebilTimes = 0;
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
     * @return StandardProduct
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
     * @return StandardProduct
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
     * @return StandardProduct
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
     * @return StandardProduct
     */
    public function setRebilTimes($rebilTimes)
    {
        $this->rebilTimes = $rebilTimes;

        return $this;
    }


}