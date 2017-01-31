<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:36.
 */
namespace Venice\AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use JMS\Serializer\Annotation\SerializedName;
use Trinity\Component\Core\Interfaces\ClientInterface;
use Trinity\Component\EntityCore\Entity\BaseUser;
use Trinity\NotificationBundle\Annotations as N;
use Trinity\NotificationBundle\Entity\NotificationEntityInterface;
use Venice\AppBundle\Entity\Interfaces\OAuthTokenInterface;
use Venice\AppBundle\Entity\Interfaces\ProductAccessInterface;
use Venice\AppBundle\Entity\Interfaces\ProductInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Entity\Product\FreeProduct;
use Venice\AppBundle\Traits\Timestampable;

/**
 * Class User.
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 */
class User extends BaseUser implements NotificationEntityInterface, UserInterface
{
    use Timestampable;

    const PREFERRED_UNITS_IMPERIAL = 'imperial';
    const PREFERRED_UNITS_METRIC = 'metric';
    const DEFAULT_PREFERRED_METRICS = self::PREFERRED_UNITS_IMPERIAL;

    /**
     * @var int
     *
     * @SerializedName("id")
     */
    protected $necktieId;

    /**
     * @var string
     */
    protected $preferredUnits;

    /**
     * @var ArrayCollection<ProductAccess>
     */
    protected $productAccesses;

    /**
     * @var DateTime
     */
    protected $birthDate;

    /**
     * @var ArrayCollection<OAuthToken>
     */
    protected $OAuthTokens;

    /**
     * @var bool
     */
    protected $locked;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->password = '';
        $this->salt = '';
        $this->productAccesses = new ArrayCollection();
        $this->OAuthTokens = new ArrayCollection();
        $this->preferredUnits = self::DEFAULT_PREFERRED_METRICS;
        $this->birthDate = new DateTime();
        $this->locked = false;
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
     * @return UserInterface
     *
     * @throws \InvalidArgumentException
     */
    public function setPreferredUnits($preferredUnits)
    {
        if ($preferredUnits !== self::PREFERRED_UNITS_METRIC || $preferredUnits !== self::PREFERRED_UNITS_IMPERIAL) {
            throw new InvalidArgumentException(
                'Preferred units has to be one of '.
                self::PREFERRED_UNITS_METRIC.
                ' or '.
                self::PREFERRED_UNITS_IMPERIAL.
                ", $preferredUnits given."
            );
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
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function addProductAccess(ProductAccessInterface $productAccess)
    {
        if (!$this->productAccesses->contains($productAccess)) {
            $this->productAccesses->add($productAccess);
        }

        return $this;
    }

    /**
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function removeProductAccess(ProductAccessInterface $productAccess)
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
     * @return UserInterface
     */
    public function setBirthDate(DateTime $birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @param int $necktieId
     *
     * @return UserInterface
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

        if (false !== $token) {
            return $token->getAccessToken();
        }
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
        $token = $this->OAuthTokens->last();

        if (false !== $token) {
            return $token->getRefreshToken();
        }
    }

    /**
     * Get the last OAuthToken object.
     *
     * @return OAuthTokenInterface|null
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
     * @param OAuthTokenInterface $OAuthToken
     *
     * @return $this
     */
    public function addOAuthToken(OAuthTokenInterface $OAuthToken)
    {
        if (!$this->OAuthTokens->contains($OAuthToken)) {
            $this->OAuthTokens->add($OAuthToken);
        }

        return $this;
    }

    /**
     * @param OAuthTokenInterface $OAuthToken
     *
     * @return $this
     */
    public function removeOAuthToken(OAuthTokenInterface $OAuthToken)
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
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasAccessToProduct(ProductInterface $product)
    {
        if (!$product->isEnabled()) {
            return false;
        }

        // Workaround of instanceof
        if ($product->getType() === FreeProduct::TYPE) {
            return true;
        }

        $access = null;

        /** @var ProductAccessInterface $productAccess */
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

    /**
     * @return boolean
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
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
     * @param ProductInterface $product
     * @param DateTime $fromDate
     * @param DateTime|null $toDate
     * @param int|null $necktieId
     *
     * @return ProductAccessInterface|null
     */
    public function giveAccessToProduct(
        ProductInterface $product,
        \DateTime $fromDate,
        \DateTime $toDate = null,
        $necktieId = null
    ) {
        $productAccess = $this->getProductAccess($product);
        $now = new \DateTime('now');

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
        elseif ($productAccess->getToDate() !== null && $productAccess->getToDate() < $now) {
            $productAccess->setToDate($toDate);
        } //3rd - set the fromDate property to given fromDate
        elseif ($productAccess->getFromDate() > $now) {
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
     * Get productAccess entity for this user and given product.
     *
     * @param ProductInterface $product
     *
     * @return ProductAccessInterface|null
     */
    public function getProductAccess(ProductInterface $product)
    {
        /** @var ProductAccessInterface $productAccess */
        foreach ($this->productAccesses as $productAccess) {
            if ($productAccess->getProduct() == $product) {
                return $productAccess;
            }
        }
    }

    /**
     * @return bool
     */
    public function getPublic()
    {
        return $this->public;
    }
}
