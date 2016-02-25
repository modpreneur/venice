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
 * @ORM\Table(name="content_iframe")
 *
 * @package AppBundle\Entity\Content
 */
class IframeContent extends Content
{
    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text", nullable=false)
     */
    protected $html;


    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }


    /**
     * @param string $html
     *
     * @return IFrameContent
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->html;
    }


    /**
     * @inheritdoc
     */
    public function getType()
    {
        return "iframe";
    }
}