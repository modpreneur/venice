<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.10.15
 * Time: 9:27.
 */
namespace Venice\AppBundle\Entity\Content;

/**
 * Class HtmlContent.
 */
class HtmlContent extends Content
{
    /**
     * @var string
     */
    protected $html;

    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
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
     * @return HtmlContent
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'html';
    }
}
