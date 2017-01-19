<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 13:07.
 */
namespace Venice\AppBundle\Entity\Content;

use Venice\AppBundle\Entity\Interfaces\PdfContentInterface;

/**
 * Class PdfContent.
 */
class PdfContent extends Content implements PdfContentInterface
{
    /**
     * @var string Url address to the file.
     */
    protected $link;

    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->link;
    }

    /**
     * Get the content type string.
     *
     * @return string
     */
    public function getType()
    {
        return 'pdf';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return void
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }
}
