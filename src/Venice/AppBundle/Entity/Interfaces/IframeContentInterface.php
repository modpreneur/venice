<?php
namespace Venice\AppBundle\Entity\Interfaces;

/**
 * Class IFrameContent.
 */
interface IframeContentInterface extends ContentInterface
{
    /**
     * @return string
     */
    public function getHtml();

    /**
     * @param string $html
     *
     * @return IframeContentInterface
     */
    public function setHtml($html);
}
