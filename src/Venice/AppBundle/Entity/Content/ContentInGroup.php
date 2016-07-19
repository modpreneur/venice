<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:09
 */

namespace Venice\AppBundle\Entity\Content;

use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\User;

/**
 * Class ContentInGroup
 * @package Venice\AppBundle\Entity\Content
 */
class ContentInGroup
{
    /**
     * @var int
     */
    protected $id;


    /**
     * @var GroupContent
     */
    protected $group;


    /**
     * @var Content
     */
    protected $content;


    /**
     * @var int
     */
    protected $delay;


    /**
     * @var int
     */
    protected $orderNumber;


    public function __construct()
    {
        $this->delay = 0;
        $this->orderNumber = 0;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return GroupContent
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * @param GroupContent $group
     *
     * @return ContentInGroup
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }


    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @param Content $content
     *
     * @return ContentInGroup
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }


    /**
     * @param int $delay
     *
     * @return ContentInGroup
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
     * @return ContentInGroup
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }


    /**
     * Check if the given user has access to this ContentInGroup.
     *
     * @param User $user
     * @param Product $product The Product which is associated with the GroupContent
     * @param bool $checkAccessToProduct Check access to product?
     *
     * @return bool true - the user has access to the parent Product and the delay of this ContentInGroup + delay < now
     */
    public function isAvailableFor(User $user, Product $product, $checkAccessToProduct = true)
    {
        $now = new \DateTime();

        if ($checkAccessToProduct && !$user->hasAccessToProduct($product)) {
            return false;
        }

        // Get time when the user was given access to this product
        $productAccess = $user->getProductAccess($product);
        if (!$productAccess) {
            return false;
        }

        // Clone product access object to avoid weird errors.
        $timeOfAccess = clone $productAccess->getFromDate();

        // Add delay to the time
        $hours = $this->getDelay();
        $timeOfAccess->add(new \DateInterval("PT{$hours}H"));

        // Check if the the of the access + delay(in hours) < now
        return $timeOfAccess < $now;
    }
}