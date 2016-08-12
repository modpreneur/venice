<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 15:57
 */

namespace Venice\AppBundle\Entity\Content;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class VideoContent
 *
 * @package Venice\AppBundle\Entity\Content
 */
class VideoContent extends AbstractPlayableContent
{
    /**
     * @var string Url to the preview image
     */
    protected $previewImage;


    /**
     * @var string Url to the video in the mobile quality
     */
    protected $videoMobile;


    /**
     * @var string Url to the video in the low quality
     */
    protected $videoLq;


    /**
     * @var string Url to the video in the high quality
     */
    protected $videoHq;


    /**
     * @var string Url to the video in the high definition
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


    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'video';
    }


    /**
     * @return string
     */
    public function getPreviewImage()
    {
        return $this->previewImage;
    }


    /**
     * @param string $previewImage
     *
     * @return VideoContent
     */
    public function setPreviewImage($previewImage)
    {
        $this->previewImage = $previewImage;

        return $this;
    }
}
