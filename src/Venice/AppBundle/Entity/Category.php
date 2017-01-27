<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 24/01/17
 * Time: 17:55.
 */

namespace Venice\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Venice\AppBundle\Entity\Interfaces\CategoryInterface;

/**
 * Class Category.
 */
class Category implements CategoryInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var ArrayCollection<BlogArticle>
     */
    protected $blogArticles;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->blogArticles = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return CategoryInterface
     */
    public function setName(string $name)
    {
        $this->name = $name;

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
     *
     * @return CategoryInterface
     */
    public function setHandle(string $handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * @return ArrayCollection<BlogArticle>
     */
    public function getBlogArticles()
    {
        return $this->blogArticles;
    }

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function addBlogArticle(BlogArticle $blogArticle)
    {
        if (!$this->blogArticles->contains($blogArticle)) {
            $this->blogArticles->add($blogArticle);
        }

        return $this;
    }

    /**
     * @param BlogArticle $blogArticle
     *
     * @return $this
     */
    public function removeBlogArticle(BlogArticle $blogArticle)
    {
        $this->blogArticles->remove($blogArticle);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
