<?php

namespace Venice\AppBundle\Entity;

use JMS\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\Core\Interfaces\EntityInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Traits\Timestampable;
use JMS\Serializer\Annotation as Serializer;

/**
 * ProductAccess.
 *
 * @UniqueEntity(fields={"user", "product"}, errorPath="product")
 */
class ProductAccess implements NotificationEntityInterface, EntityInterface
{
    use Timestampable;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @SerializedName("id")
     */
    protected $necktieId;

    /**
     * @var User
     * @Serializer\Exclude()
     */
    private $user;

    /**
     * @var Product
     * @Serializer\Exclude()
     */
    private $product;

    /**
     * @var \DateTime
     */
    private $fromDate;

    /**
     * @var \DateTime
     */
    private $toDate;

    /**
     * ProductAccess constructor.
     */
    public function __construct()
    {
        $this->updateTimestamps();
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->toDate && $this->fromDate > $this->toDate) {
            $context
                ->buildViolation('To date must be greater than From date.')
                ->atPath('toDate')
                ->addViolation();
        }
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
     * Set user.
     *
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\User")
     *
     * @param User $user
     *
     * @return ProductAccess
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @N\AssociationGetter
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set product.
     *
     * @N\AssociationSetter(targetEntity="Venice\AppBundle\Entity\Product\Product")
     *
     * @param Product $product
     *
     * @return ProductAccess
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @N\AssociationGetter
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set fromDate.
     *
     * @param \DateTime $fromDate
     *
     * @return ProductAccess
     */
    public function setFromDate(\DateTime $fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate.
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate.
     *
     * @param \DateTime $toDate
     *
     * @return ProductAccess
     */
    public function setToDate(\DateTime $toDate = null)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate.
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
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
     * @return ProductAccess
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
     * @return string
     */
    public function __toString()
    {
        return $this->user->getFullNameOrUsername().'\'s product access for'.$this->product->getName();
    }
}
