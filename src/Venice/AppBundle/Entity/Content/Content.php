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
use Venice\AppBundle\Entity\Interfaces\ContentInterface;
use Venice\AppBundle\Entity\Interfaces\ContentProductInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Traits\Timestampable;

/**
 * Class AbstractContent.
 */
abstract class Content implements EntityInterface, ContentInterface
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
     */
    protected $contentProducts;

    /**
     * @var UserInterface
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param UserInterface $author
     *
     * @return ContentInterface
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
     * Creates new instance of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     * @param array $args
     *
     * @return ContentInterface
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
     * @return ContentInterface
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
