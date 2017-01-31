<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface InvoiceInterface
 * @package Venice\AppBundle\Entity\Interfaces
 */
interface InvoiceInterface extends BaseEntityInterface
{
    /**
     * @param int $id
     *
     * @return InvoiceInterface
     */
    public function setId($id);

    /**
     * @return float
     */
    public function getTotalPrice();

    /**
     * @param float $totalPrice
     *
     * @return InvoiceInterface
     */
    public function setTotalPrice($totalPrice);

    /**
     * @return \DateTime
     */
    public function getTransactionTime();

    /**
     * @param \DateTime $transactionTime
     *
     * @return InvoiceInterface
     */
    public function setTransactionTime(\DateTime $transactionTime);

    /**
     * @return string
     */
    public function getTransactionType();

    /**
     * @param string $transactionType
     *
     * @return InvoiceInterface
     */
    public function setTransactionType($transactionType);

    /**
     * @return ArrayCollection<string>
     */
    public function getItems();

    /**
     * @param string $item
     *
     * @return $this
     */
    public function addItem($item);

    /**
     * @param string $item
     *
     * @return $this
     */
    public function removeItem($item);
}