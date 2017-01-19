<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\ORM\PersistentCollection;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BasePaySystem;
use Trinity\Component\EntityCore\Entity\BasePaySystemVendor;


/**
 * Class PaySystem
 */
interface PaySystemInterface
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
     * @return BasePaySystem
     */
    public function setName($name);

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * @return PersistentCollection
     */
    public function getVendors();

    /**
     * @return PersistentCollection
     */
    public function getVendorItems();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return BasePaySystemVendor
     */
    public function getDefaultVendor();

    /**
     * @param BasePaySystemVendor $defaultVendor
     */
    public function setDefaultVendor($defaultVendor);

    /** @return ClientInterface[] */
    public function getClients();

    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * @param int $necktieId
     */
    public function setNecktieId(int $necktieId);

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