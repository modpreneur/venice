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
    /**
     * @var string Url address to the file.
     */
    protected $link;

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'pdf';
    }
}
