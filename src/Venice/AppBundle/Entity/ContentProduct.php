<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49.
 */
namespace Venice\AppBundle\Entity;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Trinity\Component\Core\Interfaces\EntityInterface;
use Venice\AppBundle\Entity\Interfaces\ContentInterface;
use Venice\AppBundle\Entity\Interfaces\ContentProductInterface;
use Venice\AppBundle\Entity\Interfaces\ProductInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Traits\Timestampable;

/**
 * Class ContentProduct.
 */
class ContentProduct implements EntityInterface, ContentProductInterface
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var ContentInterface
     * @Serializer\Exclude()
     */
    protected $content;

    /**
     * @var ProductInterface
     * @Serializer\Exclude()
     */
    protected $product;

    /**
     * @var int delay in hours
     */
    protected $delay;

    /**
     * @var
     */
    protected $orderNumber;

    public function __construct()
    {
        $this->content = null;
        $this->product = null;
        $this->delay = 0;
        $this->orderNumber = 0;
        $this->updateTimestamps();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ContentInterface
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param ContentInterface $content
     *
     * @return ContentProductInterface
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     *
     * @return ContentProductInterface
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return int delay in hours
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay delay in hours
     *
     * @return ContentProductInterface
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param int $orderNumber
     *
     * @return ContentProductInterface
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Check if the given user has access to this contentProduct.
     *
     * @param UserInterface $user
     * @param bool $checkAccessToProduct Check access to product
     *
     * @return bool true - the user has access to the parent product and the delay of this contentProduct + delay < now
     */
    public function isAvailableFor(UserInterface $user, $checkAccessToProduct = true)
    {
        $product = $this->getProduct();
        $now = new \DateTime();

        if ($checkAccessToProduct && !$user->hasAccessToProduct($this->product)) {
            return false;
        }

        // Get time when the user was given access to this product
        $productAccess = $user->getProductAccess($product);
        if (!$productAccess) {
            return false;
        }
        // Clone product access Datetime object to avoid weird errors.
        $timeOfAccess = clone $productAccess->getFromDate();

        // Add delay to the time
        $hours = $this->getDelay();
        $timeOfAccess->add(new \DateInterval("PT{$hours}H"));

        // Check if the the of the access + delay(in hours) < now
        return $timeOfAccess < $now;
    }

    /**
     * @param UserInterface $user
     *
     * @return DateTime|null
     */
    public function willBeAvailableOn(UserInterface $user)
    {
        if ($this->isAvailableFor($user)) {
            // 0 hours to access
            return new \DateInterval('PT0H');
        }

        $productAccess = $user->getProductAccess($this->product);

        if (!$productAccess) {
            return;
        }

        // Clone product access Datetime object to avoid weird errors.
        $timeOfAccess = clone $productAccess->getFromDate();

        // Add delay to the time
        $hours = $this->getDelay();

        return $timeOfAccess->add(new \DateInterval("PT{$hours}H"));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->content->getName().' in '.$this->product->getName();
    }
}
