<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 13:07.
 */
namespace Venice\AppBundle\Entity\Content;

/**
 * Class PdfContent.
 */
class PdfContent extends Content
{
    const TYPE = 'pdf';

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
