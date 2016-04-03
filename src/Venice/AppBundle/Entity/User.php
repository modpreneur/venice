<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36
 */

namespace Venice\AppBundle\Entity;

use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Traits\HasNotificationStatusTrait;
use Venice\AppBundle\Traits\Timestampable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Trinity\FrameworkBundle\Entity\BaseUser as TrinityUser;
use Trinity\FrameworkBundle\Entity\ClientInterface;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;


/**
 * Class User
 *
 * @ORM\Entity(repositoryClass="Venice\AppBundle\Entity\Repositories\UserRepository")
 * @ORM\Table(name="user")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website, country, region, city, addressLine1, addressLine2, postalCode")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 *
 * @package Venice\AppBundle\Entity
 */
class User extends TrinityUser implements NotificationEntityInterface
{
    use HasNotificationStatusTrait;
    use Timestampable;

    const PREFERRED_UNITS_IMPERIAL = "imperial";
    const PREFERRED_UNITS_METRIC = "metric";
    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_UNITS_IMPERIAL;

    /**
     * @var integer
     *
     * @ORM\Column(name="necktie_id", type="integer", unique=true, nullable=true)
     * @SerializedName("id")
     */
    protected $necktieId;


    /**
     * @var integer
     *
     * @ORM\Column(name="amember_id", type="integer", unique=true, nullable=true)
     */
    protected $amemberId;


    /**
     * @var string
     *
     * @ORM\Column(name="preferred_units", type="string", length=10)
     */
    protected $preferredUnits;


    /**
     * @var ArrayCollection<ProductAccess>
     *
     * @ORM\OneToMany(targetEntity="Venice\AppBundle\Entity\ProductAccess", mappedBy="user", cascade={"REMOVE", "PERSIST"})
     */
    protected $productAccesses;


    /**
     * @var DateTime
     *
     * @Assert\DateTime()
     *
     * @ORM\Column(name="date_of_birth", type="date")
     */
    protected $birthDate;


    /**
     * @var ArrayCollection<OAuthToken>
     *
     * @ORM\OneToMany(targetEntity="Venice\AppBundle\Entity\OAuthToken", mappedBy="user", cascade={"REMOVE", "PERSIST"})
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
        $this->updateTimestamps();
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
        if ($preferredUnits !== self::PREFERRED_UNITS_METRIC || $preferredUnits !== self::PREFERRED_UNITS_IMPERIAL) {
            throw new InvalidArgumentException(
                "Preferred units has to be one of ".
                self::PREFERRED_UNITS_METRIC.
                " or ".
                self::PREFERRED_UNITS_IMPERIAL.
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
        if (!$this->productAccesses->contains($productAccess)) {
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
     * @return null|string
     */
    public function getLastAccessToken()
    {
        $token = $this->OAuthTokens->last();

        if(false !== $token) {
            return $token->getAccessToken();
        }

        return null;
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
        if (!$this->OAuthTokens->contains($OAuthToken)) {
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
        if ($this->getFullName()) {
            return $this->getFullName();
        } else {
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
        $access = null;

        if (!$product->isEnabled()) {
            return false;
        }

        /** @var ProductAccess $productAccess */
        foreach ($this->getProductAccesses() as $productAccess) {
            if ($productAccess->getProduct()->getId() === $product->getId()) {
                $access = $productAccess;
            }
        }

        if (!$access) {
            return false;
        }

        $timeOfStart = $access->getFromDate();
        $timeOfEnd = $access->getToDate();
        $now = new \DateTime();

        if (!$timeOfEnd) {
            return $timeOfStart < $now;
        } else {
            return $timeOfStart < $now && $timeOfEnd > $now;
        }
    }


//    /**
//     * @param Product $product
//     * @param DateTime $fromDate
//     * @param DateTime|null $toDate
//     * @param int|null $necktieId
//     *
//     * @return ProductAccess|null
//     */
//    public function giveAccessToProduct(Product $product, \DateTime $fromDate, \DateTime $toDate = null, $necktieId = null)
//    {
//
//        //create a new ProductAccess record
//        if (!$this->hasAccessToProduct($product)) {
//            $productAccess = new ProductAccess();
//            $productAccess
//                ->setNecktieId($necktieId)
//                ->setProduct($product)
//                ->setUser($this)
//                ->setFromDate($fromDate)
//                ->setToDate($toDate);
//
//            $this->addProductAccess($productAccess);
//
//            return $productAccess;
//        } //edit existing product access record
//        else {
//            /** @var ProductAccess $productAccess */
//            foreach ($this->getProductAccesses() as $productAccess) {
//                if ($necktieId) {
//                    if ($productAccess->getNecktieId() == $necktieId) {
//                        $productAccess->setProduct($product);
//                        $productAccess->setUser($this);
//                        $productAccess->setFromDate($fromDate);
//                        $productAccess->setToDate($toDate);
//                    }
//                } else {
//                    if ($productAccess->getProduct() == $product) {
//                        $productAccess->setProduct($product);
//                        $productAccess->setUser($this);
//                        $productAccess->setFromDate($fromDate);
//                        $productAccess->setToDate($toDate);
//
//                        return $productAccess;
//                    }
//                }
//
//            }
//        }
//
//        return null;
//    }

    /**
     * @param Product $product
     * @param DateTime $fromDate
     * @param DateTime|null $toDate
     * @param int|null $necktieId
     *
     * @return ProductAccess|null
     */
    public function giveAccessToProduct(Product $product, \DateTime $fromDate, \DateTime $toDate = null, $necktieId = null)
    {
        $productAccess = $this->getProductAccess($product);
        $now = new \DateTime("now");

        // If the user does not have an access - 3 situations:
        // 1st - the productAccess between this user and product does not exist
        // 2nd - the productAccess between this user and product exists but it has already expired(toDate < now)
        // 3rd - the productAccess between this user and product exists but the fromDate > now

        // 1st - create a new ProductAccess entity
        if (!$productAccess) {
            $productAccess = new ProductAccess();
            $productAccess
                ->setNecktieId($necktieId)
                ->setProduct($product)
                ->setUser($this)
                ->setFromDate($fromDate)
                ->setToDate($toDate);

            $this->addProductAccess($productAccess);

            return $productAccess;
        } // 2nd - set the toDate property to given toDate
        else if ($productAccess->getToDate() !== null && $productAccess->getToDate() < $now) {
            $productAccess->setToDate($toDate);
        } //3rd - set the fromDate property to given fromDate
        else if ($productAccess->getFromDate() > $now) {
            $productAccess->setFromDate($fromDate);
        }

        return $productAccess;
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


    /**
     * Get productAccess entity for this user and given product.
     *
     * @param Product $product
     * @return ProductAccess|null
     */
    public function getProductAccess(Product $product)
    {
        /** @var ProductAccess $productAccess */
        foreach ($this->productAccesses as $productAccess) {
            if ($productAccess->getProduct() == $product) {
                return $productAccess;
            }
        }

        return null;
    }
}