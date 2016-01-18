<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Product\Product;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ProductAccess
 *
 * @ORM\Table(name="product_access")
 * @ORM\Entity()
 *
 * @UniqueEntity(fields={"user", "product"})
 */
class ProductAccess
{
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="productAccesses", cascade={"PERSIST"})
     */
    private $user;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="productAccesses")
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
     * @ORM\Column(name="to_date", type="datetimetz", nullable=true)
     */
    private $toDate;


    public function __construct()
    {
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

