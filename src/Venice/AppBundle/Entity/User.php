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
use Venice\AppBundle\Entity\Product\FreeProduct;
use Venice\AppBundle\Entity\Product\Product;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class User.
 *
 * @N\Source(columns="necktieId, username, email, firstName, lastName, avatar, locked, phoneNumber, website")
 * Users cannot be created on client so there is no need to use POST
 * @N\Methods(types={"put", "delete"})
 */
class User extends BaseUser implements NotificationEntityInterface
{
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
     * @Serializer\Exclude()
     */
    protected $OAuthTokens;

    /**
     * @var ArrayCollection<BlogArticle>
     * @Serializer\Exclude()
     */
    protected $blogArticles;

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
     * @return User
     *
     * @throws \InvalidArgumentException
     */
    public function setPreferredUnits($preferredUnits)
    {
        if ($preferredUnits !== self::PREFERRED_UNITS_METRIC && $preferredUnits !== self::PREFERRED_UNITS_IMPERIAL) {
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
     * @return ArrayCollection
     */
    public function getProducts()
    {
        $products = new ArrayCollection();
        foreach ($this->productAccesses as $productAccess) {
            $products->add($productAccess);
        }

        return $products;
    }

    /**
     * @param ProductAccess $productAccess
     *
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
     *
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
        return null;
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
     * @param OAuthToken $oauthToken
     *
     * @return $this
     */
    public function addOAuthToken(OAuthToken $oauthToken)
    {
        if (!$this->OAuthTokens->contains($oauthToken)) {
            $this->OAuthTokens->add($oauthToken);
        }

        return $this;
    }

    /**
     * @param OAuthToken $oauthToken
     *
     * @return $this
     */
    public function removeOAuthToken(OAuthToken $oauthToken)
    {
        $this->OAuthTokens->remove($oauthToken);

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
        if (!$product->isEnabled()) {
            return false;
        }

        // Workaround of instanceof
        if ($product->getType() === FreeProduct::TYPE) {
            return true;
        }

        $access = null;

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

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }

    /**
     * Get productAccess entity for this user and given product.
     *
     * @param Product $product
     *
     * @return ProductAccess|null
     */
    public function getProductAccess(Product $product)
    {
        /** @var ProductAccess $productAccess */
        foreach ($this->productAccesses as $productAccess) {
            if ($productAccess->getProduct() === $product) {
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

    /**
     * @return ArrayCollection<BlogArticle>
     */
    public function getBlogArticles()
    {
        return $this->blogArticles;
    }

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function addBlogArticle(BlogArticle $blogArticle)
    {
        if (!$this->blogArticles->contains($blogArticle)) {
            $this->blogArticles->add($blogArticle);
        }

        return $this;
    }

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function removeBlogArticle(BlogArticle $blogArticle)
    {
        $this->blogArticles->remove($blogArticle);

        return $this;
    }
}
