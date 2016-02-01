<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 13:28
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\Content\Content;
use AppBundle\Entity\ContentProduct;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use AppBundle\Form\Product\FreeProductType;
use AppBundle\Form\Product\StandardProductType;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Trinity\FrameworkBundle\Entity\BaseProduct as TrinityProduct;

/**
 * Class BaseProduct
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="ProductRepository", )
 *
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 *
 * @UniqueEntity("handle")
 *
 * @package AppBundle\Entity\Product
 */
abstract class Product extends TrinityProduct
{
    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", unique=true)
     */
    protected $handle;


    /**
     * @var string Url to image of the product
     *
     * @Assert\Url()
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    protected $image;


    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;


    /**
     * @var integer
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 10000
     *     )
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    protected $orderNumber;


    /**
     * @var ArrayCollection<ProductAccess>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="product", cascade={"remove"})
     */
    protected $productAccesses;


    /**
     * @var ArrayCollection<ContentProduct>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="product", cascade={"remove"})
     * @OrderBy({"delay" = "ASC", "orderNumber" = "ASC"})
     *
     */
    protected $contentProducts;


    public function __construct()
    {
        $this->enabled = true;
        $this->productAccesses = new ArrayCollection();
        $this->orderNumber = 0;
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }


    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }


    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source)
    {
        $this->handle = (new Slugify())->slugify($source);
    }


    public function setName($name)
    {
        $this->name = $name;
        $this->createHandle($name);
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param bool $enabled
     *
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

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
     * @return Product
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

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
     * @return ArrayCollection<ContentProduct>
     */
    public function getContentProducts()
    {
        return $this->contentProducts;
    }


    /**
     * @param ContentProduct $contentProduct
     * @return $this
     */
    public function addContentProduct(ContentProduct $contentProduct)
    {
        if (!$this->contentProducts->contains($contentProduct)) {
            $this->contentProducts->add($contentProduct);
        }

        return $this;
    }


    /**
     * @param ContentProduct $contentProduct
     * @return $this
     */
    public function removeContentProduct(ContentProduct $contentProduct)
    {
        $this->contentProducts->remove($contentProduct);

        return $this;
    }


    /**
     * Creates new instance of product from type (first part of entity name ends with Product)
     *
     * @param string $type Could be formatted like StandardProduct, FreeProduct, AppBundle\\Entity\\Product\\StandardProduct, ...
     * @param array $args
     *
     * @return Product
     */
    public static function createProductByType($type, $args = [])
    {
        $type = ucfirst($type);

        if (!strpos($type, "Product"))
            $type .= "Product";

        if (!strpos($type, "AppBundle\\Entity\\Product\\"))
            $type = "AppBundle\\Entity\\Product\\".$type;

        $class = new \ReflectionClass($type);

        return $class->newInstanceArgs($args);
    }


    /**
     * Get the product type string
     *
     * @return string
     */
    abstract public function getType();


    /**
     * Get form type of product
     *
     * @return StandardProductType|FreeProductType
     */
    public function getFormTypeClass()
    {
        $name = get_class($this)."Type";
        $name = str_replace('Entity', 'Form', $name);

        return $name;
    }


    /**
     * Get all Content of the product.
     *
     * @internal potentially dangerous - does not check the delay and product access!
     *
     * @return Content[]
     */
    public function getAllContent()
    {
        $content = [];

        /** @var ContentProduct $contentProduct */
        foreach ($this->getContentProducts() as $contentProduct) {
            $content[] = $contentProduct->getContent();
        }

        return $content;
    }


    /**
     * Get all content by type.
     *
     * @internal potentially dangerous - does not check the delay and product access!
     *
     * @param string $type Type of the content (html, text, video, mp3, ...)
     *
     * @return array
     */
    public function getAllContentByType($type)
    {
        $content = [];

        /** @var ContentProduct $contentProduct */
        foreach ($this->contentProducts as $contentProduct) {
            if ($contentProduct->getContent()->getType() === $type) {
                $content[] = $content;
            }
        }

        return $content;
    }


    /**
     * Get all available content without information about delay and order.
     *
     * @param User $user
     *
     * @return Content[]
     */
    public function getAllAvailableContent(User $user)
    {
        $content = [];

        foreach ($this->getAvailableContentProducts($user) as $availableContentProduct) {
            $content[] = $availableContentProduct->getContent();
        }

        return $content;
    }


    /**
     * Get all available content by type without information about delay and order.
     *
     * @param User $user
     * @param string $type Type of the content (html, text, video, mp3, ...)
     *
     * @return Content[]
     */
    public function getAvailableContentByType(User $user, $type)
    {
        $content = [];

        foreach ($this->getAvailableContentProducts($user) as $availableContentProduct) {
            if ($availableContentProduct->getContent()->getType() === $type) {
                $content[] = $availableContentProduct->getContent();
            }
        }

        return $content;
    }


    /**
     * Get all available ContentProducts. Check if the user has access to the content.
     *
     * @param User $user
     *
     * @return ContentProduct[]
     */
    public function getAvailableContentProducts(User $user)
    {
        $contentProducts = [];

        if (!$user->hasAccessToProduct($this)) {
            return [];
        }

        /** @var ContentProduct $contentProduct */
        foreach ($this->contentProducts as $contentProduct) {
            if ($contentProduct->isAvailableFor($user, true)) {
                $contentProducts[] = $contentProduct;
            }
        }

        return $contentProducts;
    }


    /**
     * Get all ContentProducts for immersion.
     *
     * Returns array of arrays of ContentProduct.
     * Example:
     * Every index of the top level array holds array of ContentProducts with the same delay
     * [
     *  0 => [  //The content products are ordered by their orderNumber
     *          // ContentProducts with delay e.g. 24(hours)
     *          0 => ContentProduct #1
     *          1 => ContentProduct #2
     *          2 => ContentProduct #2
     *       ]
     * 1 => [  //The content products are ordered by their orderNumber
     *          // ContentProducts with delay e.g. 48(hours)
     *          0 => ContentProduct #4
     *          1 => ContentProduct #5
     *          2 => ContentProduct #6
     *       ]
     * ]
     *
     * @return array
     */
    public function getAllContentProductsForImmersion()
    {
        if ($this->contentProducts->count() === 0) {
            return [];
        }

        $content = [];
        $index = 0;
        $lastDelay = $this->contentProducts[0];

        //Group the content products
        /** @var ContentProduct $contentProduct */
        foreach ($this->contentProducts as $contentProduct) {
            if ($lastDelay === $contentProduct->getDelay()) {
                $content[$index][] = $contentProduct;
            } else {
                $index++;
                $content[$index][] = $contentProduct;
                $lastDelay = $contentProduct->getDelay();
            }
        }

        // Reindex the array
        return array_values($content);
    }

}