<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:47
 */

namespace Venice\AppBundle\Entity;

use Trinity\Component\Core\Interfaces\EntityInterface;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Traits\Timestampable;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class BlogArticle
 */
class BlogArticle implements EntityInterface
{
    use Timestampable;

    /**
     * @var int Used for creating a preview
     */
    protected $lastAllowedDotPosition = 200;


    /**
     * @var int Used for creating a preview
     */
    protected $maxCountOfCharacters = 400;


    /**
     * @var int
     */
    protected $id;


    /**
     * @var string
     */
    protected $handle;


    /**
     * @var User
     */
    protected $publisher;


    /**
     * @var DateTime
     */
    protected $dateToPublish;


    /**
     * @var string
     */
    protected $title;


    /**
     * @var string
     */
    protected $content;

    /**
     * @var ArrayCollection<Product>
     */
    protected $products;


    public function __construct()
    {
        $this->updateTimestamps();
        $this->products = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     */
    public function setHandle($handle)
    {
        if($handle !== null)
        {
            $this->handle = $handle;
        }
    }


    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source)
    {
        $this->handle = (new Slugify())->slugify($source);
    }


    /**
     * Get publisher
     *
     * @return User
     */
    public function getPublisher()
    {
        return $this->publisher;
    }


    /**
     * Set publisher
     *
     * @param User $publisher
     * @return BlogArticle
     */
    public function setPublisher(User $publisher)
    {
        $this->publisher = $publisher;

        return $this;
    }


    /**
     * Get dateToPublish
     *
     * @return DateTime
     */
    public function getDateToPublish()
    {
        return $this->dateToPublish;
    }


    /**
     * Set dateToPublish
     *
     * @param DateTime $dateToPublish
     * @return BlogArticle
     */
    public function setDateToPublish(DateTime $dateToPublish)
    {
        $this->dateToPublish = $dateToPublish;

        return $this;
    }


    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return BlogArticle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        $this->createHandle($title);

        return $this;
    }


    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * Set content
     *
     * @param string $content
     * @return BlogArticle
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * Check if the article will be available in given DateTime.
     *
     * @param DateTime|null $dateTime If null check if the article is available now.
     *
     * @return bool
     */
    public function isPublished(DateTime $dateTime = null)
    {
        if($dateTime != null)
        {
            return $this->dateToPublish >= $dateTime;
        }
        else
        {
            return $this->dateToPublish >= new DateTime();
        }
    }

    /**
     * Get preview of the article. Returns the first paragraph of a few first sentences.
     *
     * @return string
     */
    public function getPreview()
    {
        $firstPEnd = strpos($this->content,"</p>");

        if($firstPEnd !== false)
        {
            $stringToFirstPEnd = strip_tags(substr($this->content,0,$firstPEnd));

            if(strlen($stringToFirstPEnd) > $this->maxCountOfCharacters)
            {
                //because we don't want to cut the sentence in half, we delete it entirely
                while(strlen($stringToFirstPEnd) > $this->maxCountOfCharacters)
                {
                    $positionOfTheLastDot = strrpos($stringToFirstPEnd, ".");
                    $stringToFirstPEnd = substr($stringToFirstPEnd, 0, $positionOfTheLastDot);
                }

                $stringToFirstPEnd .= ".";
            }

            return $stringToFirstPEnd;
        }
        else
        {
            $offset = min($this->lastAllowedDotPosition, strlen($this->content));
            $dotPosition = strpos($this->content, ".", $offset);

            if($dotPosition === false)
            {
                $length = min($this->maxCountOfCharacters, strlen($this->content));
                return substr($this->content, 0, $length) . "...";
            }
            else
            {
                return substr($this->content, 0, $dotPosition + 1);
            }
        }

    }


    /**
     * @return ArrayCollection<Product>
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product)
    {
        if(!$this->products->contains($product))
        {
            $product->addBlogArticle($this);
            $this->products->add($product);
        }

        return $this;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function removeProduct(Product $product)
    {
        $this->products->remove($product);
        $product->removeBlogArticle($this);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}