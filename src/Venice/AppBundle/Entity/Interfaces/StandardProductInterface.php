<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Venice\AppBundle\Entity\BillingPlan;

/**
 * Interface StandardProductInterface
 *
 * @package Venice\AppBundle\Entity\Interfaces
 */
interface StandardProductInterface extends ProductInterface
{
    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * @param int $necktieId
     *
     * @return StandardProductInterface
     */
    public function setNecktieId($necktieId);

    /** @return ClientInterface[] */
    public function getClients();

    /**
     * @return boolean
     */
    public function isPurchasable() : bool;

    /**
     * @param boolean $purchasable
     */
    public function setPurchasable(bool $purchasable);

    /**
     * @return BillingPlan
     */
    public function getNecktieDefaultBillingPlan();

    /**
     * @param BillingPlan $billingPlan
     */
    public function setNecktieDefaultBillingPlan($billingPlan);

    /**
     * @return BillingPlan
     */
    public function getVeniceDefaultBillingPlan();

    /**
     * @param BillingPlan $billingPlan
     */
    public function setVeniceDefaultBillingPlan($billingPlan);

    /**
     * Get venice default billing plan if provided. If not get necktie default billing plan.
     *
     * @return BillingPlan
     */
    public function getDefaultBillingPlan();

    /**
     * @return string
     */
    public function getNecktieDescription();

    /**
     * @param string $necktieDescription
     */
    public function setNecktieDescription($necktieDescription);

    /**
     * Get venice description if provided. If not get necktie description.
     *
     * @return string
     */
    public function getDescriptionForCustomer();

    /**
     * @return ArrayCollection<BillingPlan>
     */
    public function getBillingPlans();

    /**
     * @param BillingPlan $billingPlan
     * @return $this
     */
    public function addBillingPlan(BillingPlan $billingPlan);

    /**
     * @param BillingPlan $billingPlan
     * @return $this
     */
    public function removeBillingPlan(BillingPlan $billingPlan);
}
