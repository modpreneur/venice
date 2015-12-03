<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:47
 */

namespace AppBundle\Entity;


use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="blog_article")
 *
 * Class BlogArticle
 * @package AppBundle\Entity
 */
class BlogArticle
{
    /**
     * @var int Used for creating a preview
     */
    private $lastAllowedDotPosition = 200;

    /**
     * @var int Used for creating a preview
     */
    private $maxCountOfCharacters = 400;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="handle", type="string", unique=true)
     * @var
     */
    protected $handle;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $publisher;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_written", type="datetime")
     */
    protected $dateWritten;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_to_publish", type="datetime", nullable=true)
     */
    protected $dateToPublish;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content;


    function __construct()
    {
        $this->dateWritten = new DateTime();
        $this->published = false;
        $this->commentsOn = true;
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
     * Get dateWritten
     *
     * @return DateTime
     */
    public function getDateWritten()
    {
        return $this->dateWritten;
    }


    /**
     * Set dateWritten
     *
     * @param DateTime $dateWritten
     * @return BlogArticle
     */
    public function setDateWritten(DateTime $dateWritten)
    {
        $this->dateWritten = $dateWritten;

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
     * @param DateTime $dateTime
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
}