<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Product\FreeProduct;
use AppBundle\Entity\Product\Product;
use \DateTime;
use \InvalidArgumentException;
use APY\DataGridBundle\Grid\Mapping\Column;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseUser as TrinityUser;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * @ORM\Table(name="user")
 *
 * @package AppBundle\Entity
 */
class User extends TrinityUser
{
    const PREFERRED_UNITS_IMPERIAL = "imperial";
    const PREFERRED_UNITS_METRIC = "metric";

    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_UNITS_IMPERIAL;

    /**
     * ORM\@Column(name="necktie_id", type="integer", unique=true, nullable=true)
     *
     * @var integer
     */
    protected $necktieId;

    /**
     * ORM\@Column(name="amember_id", type="integer", unique=true, nullable=true)
     *
     * @var integer
     */
    protected $amemberId;

    /**
     * @ORM\Column(name="preferred_units", type="string", length=10)
     *
     * @var string
     */
    protected $preferredUnits;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="user", cascade={"REMOVE", "PERSIST"})
     *
     * @var ArrayCollection<ProductAccess>
     */
    protected $productAccesses;

    /**
     * @ORM\Column(name="date_of_birth", type="date")
     *
     * @var DateTime
     */
    protected $birthDate;


    /**
     * @var ArrayCollection<OAuthToken>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\OAuthToken", mappedBy="user", cascade={"remove", "persist"})
     */
    protected $OAuthTokens;


    public function __construct()
    {
        parent::__construct();

        $this->password = "";
        $this->salt = "";
        $this->productAccesses = new ArrayCollection();
        $this->OAuthTokens = new ArrayCollection();
        $this->preferredUnits = self::DEFAULT_PREFERRED_METRICS;
        $this->birthDate = new DateTime();
    }


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
     * @return ArrayCollection<ProductAccess>
     */
    public function getProductAccesses()
    {
        return $this->productAccesses;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function addProductAccess(ProductAccess $productAccess)
    {
        if(!$this->productAccesses->contains($productAccess))
        {
            $this->productAccesses->add($productAccess);
        }

        return $this;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function removeProductAccess(ProductAccess $productAccess)
    {
        $this->productAccesses->remove($productAccess);

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


    /**
     * @param int $amemberId
     *
     * @return User
     */
    public function setAmemberId($amemberId)
    {
        $this->amemberId = $amemberId;

        return $this;
    }


    /**
     * @param int $necktieId
     *
     * @return User
     */
    public function setNecktieId($necktieId)
    {
        $this->necktieId = $necktieId;

        return $this;
    }


    /**
     * Get the last refresh token string.
     *
     * @return string
     */
    public function getLastAccessToken()
    {
        return $this->OAuthTokens->last()->getAccessToken();
    }


    /**
     * Check if the last OAuth access token is valid based on stored lifetime.
     *
     * @return bool
     */
    public function isLastAccessTokenValid()
    {
        return $this->getLastToken()->isAccessTokenValid();
    }


    /**
     * Get the last refresh token string.
     *
     * @return string
     */
    public function getLastRefreshToken()
    {
        return $this->OAuthTokens->last()->getRefreshToken();
    }


    /**
     * Get the last OAuthToken object.
     *
     * @return OAuthToken|null
     */
    public function getLastToken()
    {
        return $this->OAuthTokens->last();
    }


    /**
     * Get all OAuthTokens.
     *
     * @return ArrayCollection<OAuthToken>
     */
    public function getOAuthTokens()
    {
        return $this->OAuthTokens;
    }


    /**
     * @param OAuthToken $OAuthToken
     * @return $this
     */
    public function addOAuthToken(OAuthToken $OAuthToken)
    {
        if(!$this->OAuthTokens->contains($OAuthToken))
        {
            $this->OAuthTokens->add($OAuthToken);
        }

        return $this;
    }


    /**
     * @param OAuthToken $OAuthToken
     * @return $this
     */
    public function removeOAuthToken(OAuthToken $OAuthToken)
    {
        $this->OAuthTokens->remove($OAuthToken);

        return $this;
    }


    /**
     * @param Product $product
     *
     * @return bool
     */
    public function hasAccessToProduct(Product $product)
    {
        if($product instanceof FreeProduct)
        {
            return true;
        }

        $today = new \DateTime();

        /** @var ProductAccess $productAccess */
        foreach($this->getProductAccesses() as $productAccess)
        {
            if($productAccess->getProduct() == $product && $productAccess->getDateFrom() < $today)
            {
                if($productAccess->getDateTo() == null)
                {
                    return true;
                }
                else if($productAccess->getDateTo() > $today)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
        }

        return false;
    }


    /**
     * @param Product       $product
     * @param DateTime      $dateFrom
     * @param DateTime|null $dateTo
     *
     * @return ProductAccess|null
     */
    public function giveAccessToProduct(Product $product, \DateTime $dateFrom, \DateTime $dateTo = null)
    {

        //create a new ProductAccess record
        if(!$this->hasAccessToProduct($product))
        {
            $productAccess = new ProductAccess();
            $productAccess
                ->setProduct($product)
                ->setUser($this)
                ->setDateFrom($dateFrom)
                ->setDateTo($dateTo);

            $this->addProductAccess($productAccess);

            return $productAccess;
        }
        //edit existing product access record
        else
        {
            /** @var ProductAccess $productAccess */
            foreach($this->getProductAccesses() as $productAccess)
            {
                if($productAccess->getProduct() == $product)
                {
                    $productAccess->setDateFrom($dateFrom);
                    $productAccess->setDateTo($dateTo);

                    return $productAccess;
                }
            }
        }

        return null;
    }

}