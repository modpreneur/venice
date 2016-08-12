<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:39
 */

namespace Venice\AppBundle\Entity\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Traits\Timestampable;

/**
 * Class AbstractContent
 *
 * @package Venice\AppBundle\Entity\Content
 */
abstract class Content
{
    use Timestampable;

    /**
     * @var int
     */
    protected $id;


    /**
     * @var string
     */
    protected $name;


    /**
     * @var ArrayCollection<ContentProduct>
     */
    protected $contentProducts;


    /**
     * @var User
     */
    protected $author;


    /**
     * @var ContentInGroup
     */
    protected $contentsInGroup;


    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     */
    abstract public function getContent();


    public function __construct()
    {
        $this->updateTimestamps();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * @param User $author
     *
     * @return Content
     */
    public function setAuthor($author)
    {
        $this->author = $author;

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
     * Creates new instance of content from type (first part of entity name ends with Content)
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     * @param array $args
     *
     * @return Content
     */
    public static function createContentByType($type, $args = [])
    {
        $class = new \ReflectionClass(static::createContentClassByType($type));

        return $class->newInstanceArgs($args);
    }

    /**
     * Return a class of content from type (first part of entity name ends with Content)
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     *
     * @return Content
     */
    public static function createContentClassByType($type)
    {
        $type = ucfirst($type);

        if (!strpos($type, 'Content')) {
            $type .= 'Content';
        }

        //todo:
        if (!strpos($type, "Venice\\AppBundle\\Entity\\Content\\")) {
            $type = "Venice\\AppBundle\\Entity\\Content\\" . $type;
        }

        return $type;
    }


    /**
     * Get the content type string
     *
     * @return string
     */
    abstract public function getType();
}
