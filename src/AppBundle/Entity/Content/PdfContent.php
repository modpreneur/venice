<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 13:07
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class PdfContent
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_pdf")
 *
 * @package AppBundle\Entity\Content
 */
class PdfContent extends BaseContent
{
    /**
     * @var string Url address to the file.
     *
     * @ORM\Column(name="link", type="string", length=255)
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

}