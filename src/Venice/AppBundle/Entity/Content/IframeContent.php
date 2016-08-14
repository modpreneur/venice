<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:36.
 */
namespace Venice\AppBundle\Entity\Content;

/**
 * Class IFrameContent.
 */
class IframeContent extends Content
{
    /**
     * @var string
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
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'iframe';
    }
}
