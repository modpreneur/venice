<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\BillingPlan;
use AppBundle\Traits\HasNotificationStatusTrait;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Trinity\FrameworkBundle\Entity\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;


/**
 * @ORM\Entity()
 * @ORM\Table(name="product_standard")
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @N\Source(columns="necktieId, name, description")
 * @N\Methods(types={"put", "post", "delete"})
 * @N\Url(postfix="product")
 *
 * @UniqueEntity(fields={"necktieId"})
 * @UniqueEntity(fields={"amemberId"})
 * @UniqueEntity(fields={"desktopBillingPlan"})
 * @UniqueEntity(fields={"mobileBillingPlan"})
 *
 * Class StandardProduct
 */
class StandardProduct extends Product implements NotificationEntityInterface
{
    use HasNotificationStatusTrait;

    const TYPE = "standard";

    /**
     * @var integer
     *
     * @ORM\Column(name="necktie_id", type="integer", nullable=true, unique=true)
     *
     * @SerializedName("id")
     */
    protected $necktieId;


    /**
     * @var integer
     *
     * @ORM\Column(name="amember_id", type="integer", nullable=true, unique=true)
     */
    protected $amemberId;


    /**
     * @var BillingPlan
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\BillingPlan", cascade={"PERSIST", "REMOVE"})
     */
    protected $desktopBillingPlan;


    /**
     * Billing plan which will be used e.g.g in mobile application.
     *
     * @var BillingPlan
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\BillingPlan", cascade={"PERSIST", "REMOVE"})
     */
    protected $mobileBillingPlan;


    public function __construct()
    {
        parent::__construct();

        $this->status = [];
    }


    /**
     * @return BillingPlan
     */
    public function getDesktopBillingPlan()
    {
        return $this->desktopBillingPlan;
    }


    /**
     * @param BillingPlan $billingPlan
     *
     * @return $this
     */
    public function setDesktopBillingPlan($billingPlan)
    {
        $this->desktopBillingPlan = $billingPlan;

        return $this;
    }


    /**
     * @return BillingPlan
     */
    public function getMobileBillingPlan()
    {
        return $this->mobileBillingPlan;
    }


    /**
     * @param BillingPlan $mobileBillingPlan
     */
    public function setMobileBillingPlan($mobileBillingPlan)
    {
        $this->mobileBillingPlan = $mobileBillingPlan;
    }


    public function getType()
    {
        return self::TYPE;
    }


    /**
     * @return int
     */
    public function getAmemberId()
    {
        return $this->amemberId;
    }


    /**
     * @param int $amemberId
     *
     * @return StandardProduct
     */
    public function setAmemberId($amemberId)
    {
        $this->amemberId = $amemberId;

        return $this;
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

}