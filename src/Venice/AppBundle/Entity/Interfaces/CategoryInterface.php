<?php

namespace Venice\AppBundle\Entity\Interfaces;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\Core\Interfaces\EntityInterface;
use Venice\AppBundle\Entity\BlogArticle;

/**
 * Class Category.
 */
interface CategoryInterface extends EntityInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return CategoryInterface
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getHandle();

    /**
     * @param string $handle
     *
     * @return CategoryInterface
     */
    public function setHandle(string $handle);

    /**
     * @return ArrayCollection<BlogArticle>
     */
    public function getBlogArticles();

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function addBlogArticle(BlogArticle $blogArticle);

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function removeBlogArticle(BlogArticle $blogArticle);
}
