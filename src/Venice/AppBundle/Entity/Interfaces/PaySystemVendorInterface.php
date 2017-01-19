<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BasePaySystem;
use Trinity\Component\EntityCore\Entity\BasePaySystemVendor;


/**
 * Class PaySystemVendor
 */
interface PaySystemVendorInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return BasePaySystemVendor
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function __toString();

    /** @return ClientInterface[] */
    public function getClients();

    /**
     * @return BasePaySystem
     */
    public function getPaySystem();

    /**
     * @param BasePaySystem $paySystem
     */
    public function setPaySystem($paySystem);

    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * @param int $necktieId
     */
    public function setNecktieId(int $necktieId);

    /**
     * @return boolean
     */
    public function isDefaultForVenice() : bool;

    /**
     * @param boolean $defaultForVenice
     */
    public function setDefaultForVenice(bool $defaultForVenice);

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