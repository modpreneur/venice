<?php

namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\Core\Interfaces\EntityInterface;

/**
 * Class Tag.
 */
interface TagInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return TagInterface
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @param string $handle
     *
     * @return TagInterface
     */
    public function setHandle(string $handle);

    /**
     * @return ArrayCollection<BlogArticleInterface>
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
     * Add this tag to the given entity.
     *
     * @param $entity EntityInterface Instance of an class which is associated with the Tag class
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the given object's class is not associated with the Tag class
     */
    public function addTo(EntityInterface $entity);

    /**
     * Remove this tag from the given entity.
     *
     * @param $entity EntityInterface Instance of an class which is associated with the Tag class
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the given object's class is not associated with the Tag class
     */
    public function removeFrom(EntityInterface $entity);
}
