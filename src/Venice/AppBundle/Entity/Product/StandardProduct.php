<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 20:32
 */

namespace Venice\AppBundle\Entity\Product;

use Venice\AppBundle\Entity\BillingPlan;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;

/**
 * @ORM\Entity(repositoryClass="StandardProductRepository")
 * @ORM\Table(name="product_standard")
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @N\Source(columns="necktieId, name, description, defaultBillingPlan")
 * Creating products on client is not allowed because creating billing plans is not allowed
 * @N\Methods(types={"put", "delete"})
 * @N\Url(postfix="product")
 *
 * @UniqueEntity(fields={"necktieId"})
 *
 * Class StandardProduct
 */
class StandardProduct extends Product implements NotificationEntityInterface
{
    const TYPE = "standard";

    /**
     * @var integer
     *
     * @ORM\Column(name="necktie_id", type="integer", nullable=false, unique=true)
     *
     * @SerializedName("id")
     */
    protected $necktieId;


    /**
     * @var BillingPlan Billing plan of the product
     *
     * @ORM\OneToOne(targetEntity="Venice\AppBundle\Entity\BillingPlan", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    protected $defaultBillingPlan;


    public function __construct()
    {
        parent::__construct();

        $this->notificationStatus = [];
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
