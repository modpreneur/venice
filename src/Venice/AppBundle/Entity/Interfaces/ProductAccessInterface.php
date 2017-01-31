<?php

namespace Venice\AppBundle\Entity\Interfaces;

use Trinity\Component\Core\Interfaces\ClientInterface;

/**
 * ProductAccess.
 */
interface ProductAccessInterface extends BaseEntityInterface
{
    /**
     * Set user.
     *
     * @param UserInterface $user
     *
     * @return ProductAccessInterface
     */
    public function setUser(UserInterface $user);

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Set product.
     *
     * @param ProductInterface $product
     *
     * @return ProductAccessInterface
     */
    public function setProduct(ProductInterface $product);

    /**
     * Get product.
     *
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * Set fromDate.
     *
     * @param \DateTime $fromDate
     *
     * @return ProductAccessInterface
     */
    public function setFromDate(\DateTime $fromDate);

    /**
     * Get fromDate.
     *
     * @return \DateTime
     */
    public function getFromDate();

    /**
     * Set toDate.
     *
     * @param \DateTime $toDate
     *
     * @return ProductAccessInterface
     */
    public function setToDate(\DateTime $toDate = null);

    /**
     * Get toDate.
     *
     * @return \DateTime
     */
    public function getToDate();

    /**
     * @return int
     */
    public function getNecktieId();

    /**
     * @param int $necktieId
     *
     * @return ProductAccessInterface
     */
    public function setNecktieId($necktieId);

    /**
     * @return ClientInterface[]
     */
    public function getClients();

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
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps();
}
