<?php
namespace Venice\AppBundle\Entity\Interfaces;

use DateTime;


/**
 * Class ContentProduct.
 */
interface ContentProductInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return ContentInterface
     */
    public function getContent();

    /**
     * @param ContentInterface $content
     *
     * @return ContentProductInterface
     */
    public function setContent($content);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     *
     * @return ContentProductInterface
     */
    public function setProduct($product);

    /**
     * @return int delay in hours
     */
    public function getDelay();

    /**
     * @param int $delay delay in hours
     *
     * @return ContentProductInterface
     */
    public function setDelay($delay);

    /**
     * @return int
     */
    public function getOrderNumber();

    /**
     * @param int $orderNumber
     *
     * @return ContentProductInterface
     */
    public function setOrderNumber($orderNumber);

    /**
     * Check if the given user has access to this contentProduct.
     *
     * @param UserInterface $user
     * @param bool $checkAccessToProduct Check access to product
     *
     * @return bool true - the user has access to the parent product and the delay of this contentProduct + delay < now
     */
    public function isAvailableFor(UserInterface $user, $checkAccessToProduct = true);

    /**
     * @param UserInterface $user
     *
     * @return DateTime|null
     */
    public function willBeAvailableOn(UserInterface $user);

    /**
     * @return string
     */
    public function __toString();

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