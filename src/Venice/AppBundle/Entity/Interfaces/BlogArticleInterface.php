<?php
namespace Venice\AppBundle\Entity\Interfaces;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Class BlogArticle.
 */
interface BlogArticleInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

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
     * Get publisher.
     *
     * @return UserInterface
     */
    public function getPublisher();

    /**
     * Set publisher.
     *
     * @param UserInterface $publisher
     *
     * @return BlogArticleInterface
     */
    public function setPublisher(UserInterface $publisher);

    /**
     * Get dateToPublish.
     *
     * @return DateTime
     */
    public function getDateToPublish();

    /**
     * Set dateToPublish.
     *
     * @param DateTime $dateToPublish
     *
     * @return BlogArticleInterface
     */
    public function setDateToPublish(DateTime $dateToPublish);

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return BlogArticleInterface
     */
    public function setTitle($title);

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return BlogArticleInterface
     */
    public function setContent($content);

    /**
     * Check if the article will be available in given DateTime.
     *
     * @param DateTime|null $dateTime If null check if the article is available now.
     *
     * @return bool
     */
    public function isPublished(DateTime $dateTime = null);

    /**
     * Get preview of the article. Returns the first paragraph of a few first sentences.
     *
     * @return string
     */
    public function getPreview();

    /**
     * @return ArrayCollection<Product>
     */
    public function getProducts();

    /**
     * @param ProductInterface $product
     *
     * @return $this
     */
    public function addProduct(ProductInterface $product);

    /**
     * @param ProductInterface $product
     *
     * @return $this
     */
    public function removeProduct(ProductInterface $product);

    /**
     * @return string
     */
    public function __toString();

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