<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Content\Content;
use AppBundle\Entity\Product\Product;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ContentProduct
 *
 * @ORM\Entity()
 * @ORM\Table(name="contents_products")
 *
 * @UniqueEntity(
 *     fields={"content", "product", "delay", "orderNumber"},
 *     errorPath="orderNumber",
 *     message="The association with the same data already exists"
 * )
 *
 * @package AppBundle\Entity
 */
class ContentProduct
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * @var Content
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\Content", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;


    /**
     * @var Product
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;


    /**
     * @var int delay in hours
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 10000
     *     )
     *
     * @ORM\Column(name="delay", type="integer", nullable=false)
     */
    protected $delay;


    /**
     * @var
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 10000
     *     )
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     */
    protected $orderNumber;


    public function __construct()
    {
        $this->content = null;
        $this->product = null;
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
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @param Content $content
     *
     * @return ContentProduct
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * @param Product $product
     *
     * @return ContentProduct
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
     * @return ContentProduct
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
     * @return ContentProduct
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Check if the given user has access to this contentProduct.
     *
     * @param User $user
     * @param bool $checkAccessToProduct Check access to product
     * @return bool true - the user has access to the parent product and the delay of this contentProduct + delay < now
     */
    public function isAvailableFor(User $user, $checkAccessToProduct = true)
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
     * @param User $user
     * @return DateTime
     */
    public function willBeAvailableOn(User $user)
    {
        if ($this->isAvailableFor($user)) {
            // 0 hours to access
            return new \DateInterval("PT0H");
        }

        $productAccess = $user->getProductAccess($this->product);

        if(!$productAccess) {
            return null;
        }

        // Clone product access Datetime object to avoid weird errors.
        $timeOfAccess = clone $productAccess->getFromDate();

        // Add delay to the time
        $hours = $this->getDelay();
        return $timeOfAccess->add(new \DateInterval("PT{$hours}H"));
    }

}