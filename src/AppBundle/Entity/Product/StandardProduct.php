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
use Doctrine\Common\Collections\ArrayCollection;
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
 * @UniqueEntity(fields={"defaultBillingPlan"})
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
     * @var ArrayCollection<BillingPlan>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BillingPlan", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $billingPlans;


    /**
     * @var BillingPlan
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\BillingPlan", cascade={"persist", "remove"})
     */
    protected $defaultBillingPlan;


    public function __construct()
    {
        parent::__construct();

        $this->notificationStatus = [];
    }


    /**
     * @return BillingPlan
     */
    public function getDefaultBillingPlan()
    {
        return $this->defaultBillingPlan;
    }


    /**
     * @param BillingPlan $defaultBillingPlan
     */
    public function setDefaultBillingPlan(BillingPlan $defaultBillingPlan)
    {
        $this->defaultBillingPlan = $defaultBillingPlan;
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
        if(!$this->billingPlans->contains($billingPlan))
        {
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




//    /**
//     * @param BillingPlan $billingPlan
//     *
//     * @return $this
//     */
//    public function setDesktopBillingPlan($billingPlan)
//    {
//        $this->desktopBillingPlan = $billingPlan;
//
//        return $this;
//    }

//
//    /**
//     * @return BillingPlan
//     */
//    public function getMobileBillingPlan()
//    {
//        return $this->mobileBillingPlan;
//    }


//    /**
//     * @param BillingPlan $mobileBillingPlan
//     */
//    public function setMobileBillingPlan($mobileBillingPlan)
//    {
//        $this->mobileBillingPlan = $mobileBillingPlan;
//    }


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

}