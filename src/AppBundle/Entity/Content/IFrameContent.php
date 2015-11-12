<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.10.15
 * Time: 9:27
 */

namespace AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;


/**
 * Class IframeContent
 * @package AppBundle\Entity\Content
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_iframe")
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
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     *
     */
    public function getContent()
    {
        return $this->html;
    }


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
     * @return IframeContent
     *
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }


    /**
     * @inheritdoc
     */
    public function getType()
    {
        return "iframe";
    }
}
