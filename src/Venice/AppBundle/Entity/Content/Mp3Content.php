<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:43.
 */
namespace Venice\AppBundle\Entity\Content;

use Venice\AppBundle\Entity\Interfaces\Mp3ContentInterface;

/**
 * Class Mp3Content.
 */
class Mp3Content extends AbstractPlayableContent implements Mp3ContentInterface
{
    /**
     * @var string Url address to the file.
     */
    protected $link;

    /**
     * @return int Duration(length) in seconds.
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $seconds Duration in seconds.
     *
     * @return $this
     */
    public function setDuration($seconds)
    {
        $this->duration = $seconds;

        return $this;
    }

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
        return 'mp3';
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
