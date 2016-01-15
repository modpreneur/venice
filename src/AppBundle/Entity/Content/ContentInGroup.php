<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 16:09
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\Table(name = "content_in_group")
 *
 * Class ContentInGroup
 * @package AppBundle\Entity\Content
 */
class ContentInGroup
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;


    /**
     * @var GroupContent
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\GroupContent", inversedBy="items")
     * @JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;


    /**
     * @var Content
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\Content", inversedBy="contentsInGroup")
     */
    protected $content;


    /**
     * @var int
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 10000
     *     )
     *
     * @ORM\Column(name="delay", type="integer", nullable=false)
     */
    protected $delay;


    /**
     * @var int
     *
     * @Assert\Range(
     *     min = 0,
     *     max = 1000
     *     )
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     */
    protected $orderNumber;


    public function __construct()
    {
        $this->delay = 0;
        $this->orderNumber = 0;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return GroupContent
     */
    public function getGroup()
    {
        return $this->group;
    }


    /**
     * @param GroupContent $group
     *
     * @return ContentInGroup
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }


    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * @param Content $content
     *
     * @return ContentInGroup
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }


    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }


    /**
     * @param int $delay
     *
     * @return ContentInGroup
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }


    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }


    /**
     * @param int $orderNumber
     *
     * @return ContentInGroup
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }
}