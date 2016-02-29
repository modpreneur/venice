<?php

namespace Venice\AppBundle\Entity;

use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * ProductAccess
 *
 * @ORM\Table(name="product_access")
 * @ORM\Entity()
 *
 * @UniqueEntity(fields={"user", "product"}, errorPath="product")
 */
class ProductAccess
{
    use Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @ORM\Column(name="necktie_id", type="integer", unique=true, nullable=true)
     *
     * @var integer
     */
    protected $necktieId;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Venice\AppBundle\Entity\User", inversedBy="productAccesses", cascade={"PERSIST"})
     */
    private $user;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Venice\AppBundle\Entity\Product\Product", inversedBy="productAccesses")
     */
    private $product;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="datetimetz", nullable=false)
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @Assert\Type("\DateTime")
     * @Assert\GreaterThanOrEqual("now")
     *
     * @ORM\Column(name="to_date", type="datetimetz", nullable=true)
     */
    private $toDate;


    public function __construct()
    {
        $this->updateTimestamps();
    }


    /**
     * @Assert\Callback
     *
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set user
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
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set product
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
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * Set fromDate
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
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }


    /**
     * Set toDate
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
     * Get toDate
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

}

