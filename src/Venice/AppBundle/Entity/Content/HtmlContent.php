<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.10.15
 * Time: 9:27.
 */
namespace Venice\AppBundle\Entity\Content;

use Venice\AppBundle\Entity\Interfaces\HtmlContentInterface;

/**
 * Class HtmlContent.
 */
class HtmlContent extends Content implements HtmlContentInterface
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
     * @return HtmlContentInterface
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
