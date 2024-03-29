<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32.
 */
namespace Venice\AppBundle\Entity\Product;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\SerializedName;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Venice\AppBundle\Entity\BillingPlan;
use JMS\Serializer\Annotation as Serializer;

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
     * @var BillingPlan Default billing plan of the product which is set on Necktie
     * @Serializer\Exclude()
     */
    protected $necktieDefaultBillingPlan;

    /**
     * @var BillingPlan Billing plan of the product which is set on Venice. It has higher priority than the Necktie one.
     * @Serializer\Exclude()
     */
    protected $veniceDefaultBillingPlan;

    /**
     * @var bool Whether the product can be bought or not
     */
    protected $purchasable;

    /**
     * @var ArrayCollection
     * @Serializer\Exclude()
     */
    protected $billingPlans;

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
     * @return BillingPlan
     */
    public function getNecktieDefaultBillingPlan()
    {
        return $this->necktieDefaultBillingPlan;
    }
    /**
     * @param BillingPlan
     */
    public function setNecktieDefaultBillingPlan($necktieDefaultBillingPlan)
    {
        $this->necktieDefaultBillingPlan = $necktieDefaultBillingPlan;
    }

    /**
     * @return BillingPlan
     */
    public function getVeniceDefaultBillingPlan()
    {
        return $this->veniceDefaultBillingPlan;
    }

    /**
     * @param BillingPlan $veniceDefaultBillingPlan
     */
    public function setVeniceDefaultBillingPlan($veniceDefaultBillingPlan)
    {
        $this->veniceDefaultBillingPlan = $veniceDefaultBillingPlan;
    }

    /**
     * Get venice default billing plan if provided. If not get necktie default billing plan.
     *
     * @return BillingPlan
     */
    public function getDefaultBillingPlan()
    {
        return $this->veniceDefaultBillingPlan ?: $this->necktieDefaultBillingPlan;
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

    /**
     * @return ArrayCollection<BillingPlan>
     */
    public function getBillingPlans()
    {
        return $this->billingPlans;
    }


    /**
     * @param BillingPlan $billingPlan
     * @return $this
     */
    public function addBillingPlan(BillingPlan $billingPlan)
    {
        if (!$this->billingPlans->contains($billingPlan)) {
            $this->billingPlans->add($billingPlan);
        }

        return $this;
    }


    /**
     * @param BillingPlan $billingPlan
     * @return $this
     */
    public function removeBillingPlan(BillingPlan $billingPlan)
    {
        $this->billingPlans->remove($billingPlan);

        return $this;
    }

}
