<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:39.
 */
namespace Venice\AppBundle\Entity\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\Core\Interfaces\EntityInterface;
use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Traits\Timestampable;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class AbstractContent.
 */
abstract class Content implements EntityInterface
{
    use Timestampable;

    const TYPE = 'abstractContent';

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
     * @Serializer\Exclude()
     */
    protected $contentProducts;

    /**
     * @var User
     * @Serializer\Exclude()
     */
    protected $author;

    /**
     * @var ContentInGroup
     * @Serializer\Exclude()
     */
    protected $contentsInGroup;

    /**
     * @var string
     */
    protected $description;

    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     */
    abstract public function getContent();

    /**
     * Content constructor.
     */
    public function __construct()
    {
        $this->updateTimestamps();
        $this->description = '';
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
     * @return int
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
     *
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
     *
     * @return $this
     */
    public function removeContentProduct(ContentProduct $contentProduct)
    {
        $this->contentProducts->remove($contentProduct);

        return $this;
    }

    /**
     * Creates new instance of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     * @param array $args
     *
     * @return Content
     */
    public static function createContentByType($type, array $args = [])
    {
        $class = new \ReflectionClass(static::createContentClassByType($type));

        return $class->newInstanceArgs($args);
    }

    /**
     * Return a class of content from type (first part of entity name ends with Content).
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

        if (!strpos($type, 'Venice\\AppBundle\\Entity\\Content\\')) {
            $type = 'Venice\\AppBundle\\Entity\\Content\\'.$type;
        }

        return $type;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get the content type string.
     *
     * @return string
     */
    abstract public function getType();
}
