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
    const TYPE = 'iframe';

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
     * @return IframeContent
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
     * Get the content type string.
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE;
    }
}
