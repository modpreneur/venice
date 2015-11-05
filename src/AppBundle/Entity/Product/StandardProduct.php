<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\BillingPlan;
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
     * @var BillingPlan
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\BillingPlan")
     */
    protected $billingPlan;

    public function __construct()
    {
        parent::__construct();

        $this->initialPrice = 0;
        $this->rebillPrice = 0;
        $this->frequency = 0;
        $this->rebilTimes = 0;
    }


    /**
     * @return BillingPlan
     */
    public function getBillingPlan()
    {
        return $this->billingPlan;
    }


    /**
     * @param BillingPlan $billingPlan
     *
     * @return $this
     */
    public function setBillingPlan($billingPlan)
    {
        $this->billingPlan = $billingPlan;

        return $this;
    }

}