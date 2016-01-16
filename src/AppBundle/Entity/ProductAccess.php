<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Product\Product;
use AppBundle\Entity\Product\StandardProduct;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAccess
 *
 * @ORM\Table(name="product_access")
 * @ORM\Entity()
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
     * @ORM\Column(name="date_to", type="datetimetz", nullable=true)
     */
    private $toDate;


    public function __construct()
    {
        $this->fromDate = new \DateTime();
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
     * @param StandardProduct $product
     *
     * @return ProductAccess
     */
    public function setProduct(StandardProduct $product)
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
    public function setDateFrom(\DateTime $fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }


    /**
     * Method to maintain compatibility with necktie.
     *
     * @param \DateTime $fromDate
     *
     * @return ProductAccess
     */
    public function setFromDate(\DateTime $fromDate)
    {
        return $this->setDateFrom($fromDate);
    }


    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getDateFrom()
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

