<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class AbstractContent.
 */
interface ContentInterface extends BaseEntityInterface
{
    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return UserInterface
     */
    public function getAuthor();

    /**
     * @param UserInterface $author
     *
     * @return ContentInterface
     */
    public function setAuthor($author);

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
     * Creates new instance of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     * @param array $args
     *
     * @return ContentInterface
     */
    public static function createContentByType($type, array $args = []);

    /**
     * Return a class of content from type (first part of entity name ends with Content).
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, Venice\AppBundle\Entity\Content\PdfContent
     *
     * @return ContentInterface
     */
    public static function createContentClassByType($type);


    /**
     * Get the content type string.
     *
     * @return string
     */
    public function getType();

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