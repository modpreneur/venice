<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:36
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class TextContent
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_text")
 *
 * @package AppBundle\Entity\Content
 */
class TextContent extends BaseContent
{
    /**
     * @var
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    protected $text;


    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }


    /**
     * @param string $text
     *
     * @return TextContent
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->text;
    }
}