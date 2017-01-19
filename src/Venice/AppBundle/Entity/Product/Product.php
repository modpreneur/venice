<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 13:28.
 */
namespace Venice\AppBundle\Entity\Product;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\EntityCore\Entity\BaseProduct;
use Venice\AppBundle\Entity\Interfaces\BlogArticleInterface;
use Venice\AppBundle\Entity\Interfaces\ContentInterface;
use Venice\AppBundle\Entity\Interfaces\ContentProductInterface;
use Venice\AppBundle\Entity\Interfaces\ProductAccessInterface;
use Venice\AppBundle\Entity\Interfaces\ProductInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;

/**
 * Class BaseProduct.
 */
abstract class Product extends BaseProduct implements ProductInterface
{
    /**
     * @var string
     */
    protected $handle;

    /**
     * @var string Url to image of the product
     */
    protected $image;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var int
     */
    protected $orderNumber;

    /**
     * @var ArrayCollection<ProductAccess>
     */
    protected $productAccesses;

    /**
     * @var ArrayCollection<ContentProduct>
     */
    protected $contentProducts;

    /**
     * @var ArrayCollection<BlogArticle>
     */
    protected $articles;

    /**
     * Get the product type string.
     *
     * @return string
     */
    abstract public function getType();

    public function __construct()
    {
        $this->enabled = true;
        $this->productAccesses = new ArrayCollection();
        $this->orderNumber = 0;
        $this->articles = new ArrayCollection();
        $this->updateTimestamps();
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

    /**
     * {@inheritdoc}
     */
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
     * @return ProductInterface
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
     * @return ProductInterface
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
     * @return ArrayCollection<ContentProduct>
     */
    public function getContentProducts()
    {
        return $this->contentProducts;
    }

    /**
     * @param ContentProductInterface $contentProduct
     *
     * @return $this
     */
    public function addContentProduct(ContentProductInterface $contentProduct)
    {
        if (!$this->contentProducts->contains($contentProduct)) {
            $this->contentProducts->add($contentProduct);
        }

        return $this;
    }

    /**
     * @param ContentProductInterface $contentProduct
     *
     * @return $this
     */
    public function removeContentProduct(ContentProductInterface $contentProduct)
    {
        $this->contentProducts->remove($contentProduct);

        return $this;
    }

    /**
     * Creates new instance of product from type (first part of entity name ends with Product).
     *
     * @param string $type Could be formatted like StandardProduct, FreeProduct,
     *                     Venice\AppBundle\\Entity\\Product\\StandardProduct, ...
     * @param array $args
     *
     * @return ProductInterface
     */
    public static function createProductByType($type, array $args = [])
    {
        $class = new \ReflectionClass(static::createProductClassByType($type));

        return $class->newInstanceArgs($args);
    }

    /**
     * Return a class of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\\Entity\Content\PdfContent
     *
     * @return ContentInterface
     */
    public static function createProductClassByType($type)
    {
        $type = ucfirst($type);

        if (!strpos($type, 'Product')) {
            $type .= 'Product';
        }

        if (!strpos($type, 'Venice\\AppBundle\\Entity\\Product\\')) {
            $type = 'Venice\\AppBundle\\Entity\\Product\\'.$type;
        }

        return $type;
    }

    /**
     * Get all Content of the product.
     *
     * @internal potentially dangerous - does not check the delay and product access!
     *
     * @return ContentInterface[]
     */
    public function getAllContent()
    {
        $content = [];

        /** @var ContentProductInterface $contentProduct */
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

        /** @var ContentProductInterface $contentProduct */
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
     * @param UserInterface $user
     *
     * @return ContentInterface[]
     */
    public function getAllAvailableContent(UserInterface $user)
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
     * @param UserInterface $user
     * @param string $type Type of the content (html, text, video, mp3, ...)
     *
     * @return ContentInterface[]
     */
    public function getAvailableContentByType(UserInterface $user, $type)
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
     * @param UserInterface $user
     *
     * @return ContentProductInterface[]
     */
    public function getAvailableContentProducts(UserInterface $user)
    {
        $contentProducts = [];

        if (!$user->hasAccessToProduct($this)) {
            return [];
        }

        /** @var ContentProductInterface $contentProduct */
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
        /** @var ContentProductInterface $contentProduct */
        foreach ($this->contentProducts as $contentProduct) {
            if ($lastDelay === $contentProduct->getDelay()) {
                $content[$index][] = $contentProduct;
            } else {
                ++$index;
                $content[$index][] = $contentProduct;
                $lastDelay = $contentProduct->getDelay();
            }
        }

        // Reindex the array
        return array_values($content);
    }

    /**
     * @return ArrayCollection<BlogArticle>
     */
    public function getBlogArticles()
    {
        return $this->articles;
    }

    /**
     * @param BlogArticleInterface $blogArticle
     *
     * @return $this
     */
    public function addBlogArticle(BlogArticleInterface $blogArticle)
    {
        if (!$this->articles->contains($blogArticle)) {
            $blogArticle->addProduct($this);
            $this->articles->add($blogArticle);
        }

        return $this;
    }

    /**
     * @param BlogArticleInterface $blogArticle
     *
     * @return $this
     */
    public function removeBlogArticle(BlogArticleInterface $blogArticle)
    {
        $this->articles->remove($blogArticle);
        $blogArticle->removeProduct($this);

        return $this;
    }
}
