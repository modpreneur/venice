<?php
namespace Venice\AppBundle\Entity\Interfaces;

/**
 * Class PdfContent.
 */
interface PdfContentInterface extends ContentInterface
{
    /**
     * @return string
     */
    public function getLink();

    /**
     * @param string $link
     *
     * @return void
     */
    public function setLink(string $link);
}