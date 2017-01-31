<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\EntityCore\Entity\BaseProduct;


/**
 * Class BaseProduct.
 */
interface ProductInterface extends BaseEntityInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return BaseProduct
     */
    public function setDescription($description);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @return string
     */
    public function __toString() : string;

    /**
     * Get the product type string.
     *
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @param string $handle
     */
    public function setHandle($handle);

    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source);

    /**
     * {@inheritdoc}
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getEnabled();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param bool $enabled
     *
     * @return ProductInterface
     */
    public function setEnabled($enabled);

    /**
     * @return int
     */
    public function getOrderNumber();

    /**
     * @param int $orderNumber
     *
     * @return ProductInterface
     */
    public function setOrderNumber($orderNumber);

    /**
     * @return ArrayCollection<ProductAccess>
     */
    public function getProductAccesses();

    /**
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function addProductAccess(ProductAccessInterface $productAccess);

    /**
     * @param ProductAccessInterface $productAccess
     *
     * @return $this
     */
    public function removeProductAccess(ProductAccessInterface $productAccess);

    /**
     * @return ArrayCollection<ContentProduct>
     */
    public function getContentProducts();

    /**
     * @param ContentProductInterface $contentProduct
     *
     * @return $this
     */
    public function addContentProduct(ContentProductInterface $contentProduct);

    /**
     * @param ContentProductInterface $contentProduct
     *
     * @return $this
     */
    public function removeContentProduct(ContentProductInterface $contentProduct);

    /**
     * Creates new instance of product from type (first part of entity name ends with Product).
     *
     * @param string $type Could be formatted like StandardProduct, FreeProduct,
     *                     Venice\AppBundle\\Entity\\Product\\StandardProduct, ...
     * @param array $args
     *
     * @return ProductInterface
     */
    public static function createProductByType($type, array $args = []);

    /**
     * Return a class of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\\Entity\Content\PdfContent
     *
     * @return ContentInterface
     */
    public static function createProductClassByType($type);

    /**
     * Get all Content of the product.
     *
     * @internal potentially dangerous - does not check the delay and product access!
     *
     * @return ContentInterface[]
     */
    public function getAllContent();

    /**
     * Get all content by type.
     *
     * @internal potentially dangerous - does not check the delay and product access!
     *
     * @param string $type Type of the content (html, text, video, mp3, ...)
     *
     * @return array
     */
    public function getAllContentByType($type);

    /**
     * Get all available content without information about delay and order.
     *
     * @param UserInterface $user
     *
     * @return ContentInterface[]
     */
    public function getAllAvailableContent(UserInterface $user);

    /**
     * Get all available content by type without information about delay and order.
     *
     * @param UserInterface $user
     * @param string $type Type of the content (html, text, video, mp3, ...)
     *
     * @return ContentInterface[]
     */
    public function getAvailableContentByType(UserInterface $user, $type);

    /**
     * Get all available ContentProducts. Check if the user has access to the content.
     *
     * @param UserInterface $user
     *
     * @return ContentProductInterface[]
     */
    public function getAvailableContentProducts(UserInterface $user);

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
    public function getAllContentProductsForImmersion();

    /**
     * @return ArrayCollection<BlogArticle>
     */
    public function getBlogArticles();

    /**
     * @param BlogArticleInterface $blogArticle
     *
     * @return $this
     */
    public function addBlogArticle(BlogArticleInterface $blogArticle);

    /**
     * @param BlogArticleInterface $blogArticle
     *
     * @return $this
     */
    public function removeBlogArticle(BlogArticleInterface $blogArticle);

    /**
     * Returns createdAt value.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Updates createdAt and updatedAt timestamps.
     */
    public function updateTimestamps();
}