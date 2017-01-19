<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Trinity\Component\Core\Interfaces\ClientInterface;
use Venice\AppBundle\Entity\BillingPlan;

/**
 * Class BillingPlan.
 */
interface BillingPlanInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getFrequency();

    /**
     * @param int $frequency
     */
    public function setFrequency($frequency);

    /**
     * @return float
     */
    public function getInitialPrice();

    /**
     * @param float $initialPrice
     */
    public function setInitialPrice($initialPrice);

    /**
     * @return float
     */
    public function getRebillPrice();

    /**
     * @param float $rebillPrice
     */
    public function setRebillPrice($rebillPrice);

    /**
     * @return int
     */
    public function getRebillTimes();

    /**
     * @param int $rebillTimes
     */
    public function setRebillTimes($rebillTimes);

    /**
     * @return int
     */
    public function getTrial();

    /**
     * @param int $trial
     */
    public function setTrial($trial);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function isRecurring();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getFrequencyString() : string;

    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * @param int $necktieId
     *
     * @return BillingPlan
     */
    public function setNecktieId($necktieId);

    /**
     * @return StandardProductInterface
     */
    public function getProduct();

    /**
     ** @param StandardProductInterface $product
     *
     * @return BillingPlan
     */
    public function setProduct($product);

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @param string $price
     *
     * @return BillingPlan
     */
    public function setPrice($price);

    /**
     * @return PaySystemVendorInterface
     */
    public function getPaySystemVendor();

    /**
     * @param PaySystemVendorInterface $paySystemVendor
     */
    public function setPaySystemVendor($paySystemVendor);

    /** @return ClientInterface[] */
    public function getClients();

    /**
     * @param $user UserInterface user representation
     * @return $this
     */
    public function setCreatedBy($user);

    /**
     * @param $user UserInterface user representation
     * @return $this
     */
    public function setUpdatedBy($user);

    /**
     * @param $user UserInterface user representation
     * @return $this
     */
    public function setDeletedBy($user);

    /**
     * @return mixed the user who created entity
     */
    public function getCreatedBy();

    /**
     * @return mixed the user who last updated entity
     */
    public function getUpdatedBy();

    /**
     * @return mixed the user who removed entity
     */
    public function getDeletedBy();

    /**
     * @return mixed
     */
    public function isBlameable();

    /**
     * Returns createdAt value.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps();
}