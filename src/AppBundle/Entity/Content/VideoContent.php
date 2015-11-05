<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:57
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;


/**
 * Class VideoContent
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_video")
 *
 * @package AppBundle\Entity\Content
 */
class VideoContent extends AbstractPlayable
{

    /**
     * @var string
     *
     * @ORM\Column(name="preview_image", type="string", length=255, nullable=true)
     */
    protected $previewImage;

    /**
     * @var string
     *
     * @ORM\Column(name="video_mob", type="string", length=255, nullable=true)
     */
    protected $videoMobile;

    /**
     * @var string
     *
     * @ORM\Column(name="video_lq", type="string", length=255, nullable=true)
     */
    protected $videoLq;

    /**
     * @var string
     *
     * @ORM\Column(name="video_hq", type="string", length=255, nullable=true)
     */
    protected $videoHq;

    /**
     * @var string
     *
     * @ORM\Column(name="video_hd", type="string", length=255, nullable=true)
     */
    protected $videoHd;


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


    public function getContent()
    {
       return $this->videoHd;
    }


    /**
     * @return string
     */
    public function getVideoMobile()
    {
        return $this->videoMobile;
    }


    /**
     * @param string $videoMobile
     *
     * @return VideoContent
     */
    public function setVideoMobile($videoMobile)
    {
        $this->videoMobile = $videoMobile;

        return $this;
    }


    /**
     * @return string
     */
    public function getVideoLq()
    {
        return $this->videoLq;
    }


    /**
     * @param string $videoLq
     *
     * @return VideoContent
     */
    public function setVideoLq($videoLq)
    {
        $this->videoLq = $videoLq;

        return $this;
    }


    /**
     * @return string
     */
    public function getVideoHq()
    {
        return $this->videoHq;
    }


    /**
     * @param string $videoHq
     *
     * @return VideoContent
     */
    public function setVideoHq($videoHq)
    {
        $this->videoHq = $videoHq;

        return $this;
    }


    /**
     * @return string
     */
    public function getVideoHd()
    {
        return $this->videoHd;
    }


    /**
     * @param string $videoHd
     *
     * @return VideoContent
     */
    public function setVideoHd($videoHd)
    {
        $this->videoHd = $videoHd;

        return $this;
    }


}