<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 24/01/17
 * Time: 17:55.
 */

namespace Venice\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Trinity\Component\Core\Interfaces\EntityInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class Tag.
 */
class Tag
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
     * @Serializer\Exclude()
     */
    protected $blogArticles;

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
     * @return Tag
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
     * @return Tag
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

    /**
     * Add this tag to the given entity.
     *
     * @param $entity EntityInterface Instance of an class which is associated with the Tag class
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the given object's class is not associated with the Tag class
     */
    public function addTo(EntityInterface $entity)
    {
        if ($entity instanceof BlogArticle) {
            $this->blogArticles->add($entity);
        } else {
            throw new \InvalidArgumentException(
                'The given instance of class'. get_class($entity) . 'is not associated with the Tag class'
            );
        }

        return $this;
    }

    /**
     * Remove this tag from the given entity.
     *
     * @param $entity EntityInterface Instance of an class which is associated with the Tag class
     *
     * @return $this
     *
     * @throws \InvalidArgumentException When the given object's class is not associated with the Tag class
     */
    public function removeFrom(EntityInterface $entity)
    {
        if ($entity instanceof BlogArticle) {
            $this->blogArticles->remove($entity);
        } else {
            throw new \InvalidArgumentException(
                'The given instance of class'. get_class($entity) . 'is not associated with the Tag class'
            );
        }
    }
}
