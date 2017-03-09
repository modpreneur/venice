<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 20.10.15
 * Time: 19:15.
 */
namespace Venice\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\Interfaces\InvoiceInterface;

class Invoice
{
    protected $id;

    protected $firstPaymentDate;

    protected $items;

    //todo @JakubFajkus this was copied from necktie and reduced... just temporally for the flofit

    //This happen in rare conditions, when user came on thank-you page and we don't have
    //the IPN yet. Necktie does not allow access on thank-you without payment information, so as for now should not
    //be created by standard invoice flow.
    /** @deprecated for migration from amember */
    const STATUS_PENDING = 'PENDING';       // Free trial, waiting for first payment

    const STATUS_NORMAL = 'NORMAL';         // Sale standard, one payment
    const STATUS_RECURRING = 'RECURRING';   // Recurring payment, can be cancelled
    const STATUS_CANCELED = 'CANCELED';     // Cancel recurring (from PENDING or RECURRING)
    const STATUS_COMPLETED = 'COMPLETED';   // After last recurring payment
    const STATUS_REFUNDED = 'REFUNDED';     // Invoice was refunded

    const VALID_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_NORMAL,
        self::STATUS_RECURRING,
        self::STATUS_CANCELED,
        self::STATUS_REFUNDED,
        self::STATUS_COMPLETED,
    ];

    /**
     * @var string
     */
    private $status = self::STATUS_NORMAL;

    /**
     * @var string
     */
    private $receipt;

    /**
     * @var string
     */
    private $stringPrice;

    /**
     * Invoice constructor.
     */
    public function __construct()
    {
        $this->items = [];
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getFirstPaymentDate(): ?\DateTime
    {
        return $this->firstPaymentDate;
    }

    /**
     * @param \DateTime $firstPayment
     */
    public function setFirstPaymentDate(\DateTime $firstPayment)
    {
        $this->firstPaymentDate = $firstPayment;
    }

    /**
     * Set receipt.
     *
     * @param string $receipt
     *
     * @return Invoice
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;

        return $this;
    }


    /**
     * Get receipt.
     *
     * @return string
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @return bool
     */
    public function canBeCancel(): bool
    {
        return $this->status === self::STATUS_RECURRING;
    }

    /**
     * For symfony form validation callback
     * @return array
     */
    public static function validStatuses()
    {
        return self::VALID_STATUSES;
    }

    /**
     * @return string
     */
    public function getStringPrice(): string
    {
        return $this->stringPrice;
    }

    /**
     * @param string $stringPrice
     */
    public function setStringPrice(string $stringPrice)
    {
        $this->stringPrice = $stringPrice;
    }


    /**
     * @return array
     */
    public static function selectStatusChoices()
    {
        return array_combine(self::VALID_STATUSES, self::VALID_STATUSES);
    }
}
