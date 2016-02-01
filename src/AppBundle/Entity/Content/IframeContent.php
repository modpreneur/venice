<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:36
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IFrameContent
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_text")
 *
 * @package AppBundle\Entity\Content
 */
class IframeContent extends Content
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    protected $text;


    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->text;
    }


    /**
     * @param string $text
     *
     * @return IFrameContent
     */
    public function setHtml($text)
    {
        $this->text = $text;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->text;
    }


    /**
     * @inheritdoc
     */
    public function getType()
    {
        return "iframe";
    }
}