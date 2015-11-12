<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:43
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class Mp3Content
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_mp3")
 *
 * @package AppBundle\Entity\Content
 */
class Mp3Content extends AbstractPlayable
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
    public function getDuration()
    {
        return $this->duration;
    }


    /**
     * {@inheritdoc}
     */
    public function setDuration($seconds)
    {
        $this->duration = $seconds;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->link;
    }


    /**
     * @inheritdoc
     */
    public function getType()
    {
        return "mp3";
    }

}