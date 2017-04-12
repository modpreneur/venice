<?php

namespace Venice\AppBundle\Entity;

/**
 * Class OrderItem
 * @package Venice\AppBundle\Entity
 */
class OrderItem
{
    /**
     * @var int Necktie id
     */
    protected $necktieId;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $productName;

    /**
     * @var int
     */
    protected $initialPrice;

    /**
     * @var int
     */
    protected $rebillPrice;

    /**
     * @var int
     */
    protected $rebillTimes;

    /**
     * @return int
     */
    public function getNecktieId(): int
    {
        return $this->necktieId;
    }

    /**
     * @param int $necktieId
     */
    public function setNecktieId(int $necktieId)
    {
        $this->necktieId = $necktieId;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     */
    public function setProductName(string $productName)
    {
        $this->productName = $productName;
    }

    /**
     * @return int
     */
    public function getInitialPrice(): int
    {
        return $this->initialPrice;
    }

    /**
     * @param int $initialPrice
     */
    public function setInitialPrice(int $initialPrice)
    {
        $this->initialPrice = $initialPrice;
    }

    /**
     * @return int
     */
    public function getRebillPrice(): int
    {
        return $this->rebillPrice;
    }

    /**
     * @param int $rebillPrice
     */
    public function setRebillPrice(int $rebillPrice)
    {
        $this->rebillPrice = $rebillPrice;
    }

    /**
     * @return int
     */
    public function getRebillTimes(): int
    {
        return $this->rebillTimes;
    }

    /**
     * @param int $rebillTimes
     */
    public function setRebillTimes(int $rebillTimes)
    {
        $this->rebillTimes = $rebillTimes;
    }
}
