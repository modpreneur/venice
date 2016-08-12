<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:43
 */

namespace Venice\AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Mp3Content
 *
 * @package Venice\AppBundle\Entity\Content
 */
class Mp3Content extends AbstractPlayableContent
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
        return 'mp3';
    }
}
