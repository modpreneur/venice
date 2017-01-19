<?php
namespace Venice\AppBundle\Entity\Interfaces;

/**
 * Class HtmlContent.
 */
interface HtmlContentInterface extends ContentInterface
{
    /**
     * @return string
     */
    public function getHtml();

    /**
     * @param string $html
     *
     * @return HtmlContentInterface
     */
    public function setHtml($html);
}
