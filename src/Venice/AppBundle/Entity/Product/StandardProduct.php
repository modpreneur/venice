<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32.
 */
namespace Venice\AppBundle\Entity\Product;

use Venice\AppBundle\Entity\BillingPlan;
use JMS\Serializer\Annotation\SerializedName;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;

/**
 * @N\Source(columns="necktieId, name")
 * Creating products on client is not allowed because creating billing plans is not allowed
 * @N\Methods(types={"put", "delete"})
 * @N\Url(postfix="product")
 *
 * Class StandardProduct
 */
class StandardProduct extends Product implements NotificationEntityInterface
{
    const TYPE = 'standard';

    /**
     * @var int
     *
     * @SerializedName("id")
     */
    protected $necktieId;

    /**
     * @var string
     */
    protected $necktieDescription;

    /**
     * @var BillingPlan Billing plan of the product
     */
    protected $defaultBillingPlan;

    /**
     * @var bool Whether the product can be bought or not
     */
    protected $purchasable;

    /**
     * StandardProduct constructor.
     */
    public function __construct()
    {
        $this->purchasable = true;
        $this->description = '';
        $this->necktieDescription = '';

        parent::__construct();
    }


    /**
     * @N\AssociationGetter
     *
     * @return BillingPlan
     */
    public function getDefaultBillingPlan()
    {
        return $this->defaultBillingPlan;
    }

    /**
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\BillingPlan")
     *
     * @param BillingPlan $defaultBillingPlan
     */
    public function setDefaultBillingPlan(BillingPlan $defaultBillingPlan)
    {
        $this->defaultBillingPlan = $defaultBillingPlan;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @return int
     */
    public function getNecktieId()
    {
        return $this->necktieId;
    }

    /**
     * @param int $necktieId
     *
     * @return StandardProduct
     */
    public function setNecktieId($necktieId)
    {
        $this->necktieId = $necktieId;

        return $this;
    }

    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }

    /**
     * @return boolean
     */
    public function isPurchasable(): bool
    {
        return $this->purchasable;
    }

    /**
     * @param boolean $purchasable
     */
    public function setPurchasable(bool $purchasable)
    {
        $this->purchasable = $purchasable;
    }

    /**
     * @return string
     */
    public function getNecktieDescription()
    {
        return $this->necktieDescription ?? '';
    }

    /**
     * @param string $necktieDescription
     */
    public function setNecktieDescription($necktieDescription)
    {
        $this->necktieDescription = $necktieDescription;
    }

    /**
     * Get venice description if provided. If not get necktie description.
     *
     * @return string
     */
    public function getDescriptionForCustomer()
    {
        return $this->description ?: $this->necktieDescription;
    }
}
