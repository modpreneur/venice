<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36
 */

namespace AppBundle\Entity;

use \DateTime;
use \InvalidArgumentException;
use AppBundle\Entity\Product\Product;
use APY\DataGridBundle\Grid\Mapping\Column;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseUser;

/**
 * Class User
 *
 * @ORM\Entity()
 * @ORM\Table(name="user")
 *
 * @package AppBundle\Entity
 */
class User extends BaseUser
{
    const PREFERRED_UNITS_IMPERIAL = "imperial";
    const PREFERRED_UNITS_METRIC = "metric";

    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_UNITS_IMPERIAL;

    /**
     * ORM\@Column(name="necktie_id", type="integer", unique=true)
     *
     * @var integer
     */
    protected $necktieId;

    /**
     * @ORM\Column(name="preferred_units", type="string", length=10)
     *
     * @var string
     */
    protected $preferredUnits;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product\Product", cascade={"remove"})
     * @ORM\JoinTable(name="user_products",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")})
     *
     * @var ArrayCollection<Product>
     */
    protected $products;

    /**
     * @ORM\Column(name="dateOfBirth", type="date")
     *
     * @var DateTime
     */
    protected $birthDate;


    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Immersion", cascade={"remove"})
     *
     * @var ArrayCollection<Immersion>
     */
    protected $immersions;


    /**
     * @return int
     */
    public function getNecktieId()
    {
        return $this->necktieId;
    }


    /**
     * @return string
     */
    public function getPreferredUnits()
    {
        return $this->preferredUnits;
    }


    /**
     * @param string $preferredUnits
     *
     * @return User
     */
    public function setPreferredUnits($preferredUnits)
    {
        if($preferredUnits !== self::PREFERRED_UNITS_METRIC || $preferredUnits !== self::PREFERRED_UNITS_IMPERIAL)
        {
            throw new InvalidArgumentException(
                "Preferred units has to be one of " .
                self::PREFERRED_UNITS_METRIC .
                " or " .
                self::PREFERRED_UNITS_IMPERIAL .
                ", $preferredUnits given.");
        }

        $this->preferredUnits = $preferredUnits;

        return $this;
    }


    /**
     * @return ArrayCollection<Product>
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * @param Product $product
     *
     * @return User
     */
    public function addProduct($product)
    {
        if(!$this->products->contains($product))
        {
            $this->products->add($product);
        }

        return $this;
    }


    /**
     * @param array|Collection $products
     *
     * @return User
     */
    public function addProducts($products)
    {
        foreach($products as $product)
        {
            $this->addProduct($product);
        }

        return $this;
    }


    public function getImmersions()
    {
        return $this->immersions;
    }


    /**
     * @param Immersion $immersion
     *
     * @return User
     */
    public function addImmersion($immersion)
    {
        if(!$this->immersions->contains($immersion))
        {
            $this->immersions->add($immersion);
        }

        return $this;
    }


    /**
     * @return DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }


    /**
     * @param DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate(DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }



}