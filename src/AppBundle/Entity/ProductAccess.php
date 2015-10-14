<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Product\Product;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAccess
 *
 * @ORM\Table(name="product_access")
 * @ORM\Entity
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="productAccesses")
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
     * @ORM\Column(name="date_from", type="datetimetz", nullable=false)
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetimetz", nullable=true)
     */
    private $dateTo;


    public function __construct()
    {
        $this->dateFrom = new \DateTime();
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
    public function setProduct($product)
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
     * Set dateFrom
     *
     * @param \DateTime $dateFrom
     *
     * @return ProductAccess
     */
    public function setDateFrom(\DateTime $dateFrom)
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }


    /**
     * Get dateFrom
     *
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }


    /**
     * Set dateTo
     *
     * @param \DateTime $dateTo
     *
     * @return ProductAccess
     */
    public function setDateTo(\DateTime $dateTo = null)
    {
        $this->dateTo = $dateTo;

        return $this;
    }


    /**
     * Get dateTo
     *
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

}

