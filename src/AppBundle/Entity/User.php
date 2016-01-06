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
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Traits\HasNotificationStateTrait;
use \DateTime;
use \InvalidArgumentException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use Trinity\FrameworkBundle\Entity\BaseUser as TrinityUser;
use Trinity\FrameworkBundle\Entity\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;

/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repositories\UserRepository")
 * @ORM\Table(name="user")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked")
 * @N\Methods(types={"put", "delete"})
 *
 * @package AppBundle\Entity
 */
class User extends TrinityUser implements NotificationEntityInterface
{
    use HasNotificationStateTrait;

    const PREFERRED_UNITS_IMPERIAL = "imperial";
    const PREFERRED_UNITS_METRIC = "metric";
    const DEFAULT_PREFERRED_METRICS = "imperial";

    /**
     * @ORM\Column(name="necktie_id", type="integer", unique=true, nullable=true)
     * @SerializedName("id")
     *
     * @var integer
     */
    protected $necktieId;


    /**
     * @ORM\Column(name="amember_id", type="integer", unique=true, nullable=true)
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


    /**
     * @var []
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $status;



    public function __construct()
    {
        parent::__construct();

        $this->password = "";
        $this->salt = "";
        $this->productAccesses = new ArrayCollection();
        $this->OAuthTokens = new ArrayCollection();
        $this->preferredUnits = self::DEFAULT_PREFERRED_METRICS;
        $this->birthDate = new DateTime();
        $this->status = [];
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
     * Get the full name of the user if set. Return username otherwise.
     *
     * @return string
     */
    public function getFullNameOrUsername()
    {
        if($this->getFullName())
        {
            return $this->getFullName();
        }
        else
        {
            return $this->getUsername();
        }
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
     * @param Product|StandardProduct $product
     * @param DateTime                $dateFrom
     * @param DateTime|null           $dateTo
     * @param int|null                $necktieId
     *
     * @return ProductAccess|null
     */
    public function giveAccessToProduct(StandardProduct $product, \DateTime $dateFrom, \DateTime $dateTo = null, $necktieId = null)
    {
        //create a new ProductAccess record
        if(!$this->hasAccessToProduct($product))
        {
            $productAccess = new ProductAccess();
            $productAccess
                ->setNecktieId($necktieId)
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
                if($necktieId)
                {
                    if($productAccess->getNecktieId() == $necktieId)
                    {
                        $productAccess->setProduct($product);
                        $productAccess->setUser($this);
                        $productAccess->setDateFrom($dateFrom);
                        $productAccess->setDateTo($dateTo);
                    }
                }
                else
                {
                    if($productAccess->getProduct() == $product)
                    {
                        $productAccess->setProduct($product);
                        $productAccess->setUser($this);
                        $productAccess->setDateFrom($dateFrom);
                        $productAccess->setDateTo($dateTo);

                        return $productAccess;
                    }
                }

            }
        }

        return null;
    }

    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }

    /**
     * @param ClientInterface $client
     * @param string $status
     * @return void
     */
    public function setSyncStatus(ClientInterface $client, $status)
    {
        $this->status[$client->getId()] = $status;
    }
}