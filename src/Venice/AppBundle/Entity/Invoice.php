<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 20.10.15
 * Time: 19:15.
 */
namespace Venice\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Invoice
{
    protected $id;

    protected $totalPrice;

    protected $transactionTime;

    protected $transactionType;

    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Invoice
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     *
     * @return Invoice
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTransactionTime()
    {
        return $this->transactionTime;
    }

    /**
     * @param \DateTime $transactionTime
     *
     * @return Invoice
     */
    public function setTransactionTime(\DateTime $transactionTime)
    {
        $this->transactionTime = $transactionTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->transactionType;
    }

    /**
     * @param string $transactionType
     *
     * @return Invoice
     */
    public function setTransactionType($transactionType)
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    /**
     * @return ArrayCollection<string>
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $item
     *
     * @return $this
     */
    public function addItem($item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    /**
     * @param string $item
     *
     * @return $this
     */
    public function removeItem($item)
    {
        $this->items->remove($item);

        return $this;
    }
}
