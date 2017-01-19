<?php
namespace Venice\AppBundle\Entity\Interfaces;

use Venice\AppBundle\Entity\Content\VideoContent;


/**
 * Class VideoContent.
 */
interface VideoContentInterface extends AbstractPlayableContentInterface
{
    /**
     * @return string
     */
    public function getVideoMobile();

    /**
     * @param string $videoMobile
     *
     * @return VideoContent
     */
    public function setVideoMobile($videoMobile);

    /**
     * @return string
     */
    public function getVideoLq();

    /**
     * @param string $videoLq
     *
     * @return VideoContent
     */
    public function setVideoLq($videoLq);

    /**
     * @return string
     */
    public function getVideoHq();

    /**
     * @param string $videoHq
     *
     * @return VideoContent
     */
    public function setVideoHq($videoHq);

    /**
     * @return string
     */
    public function getVideoHd();

    /**
     * @param string $videoHd
     *
     * @return VideoContent
     */
    public function setVideoHd($videoHd);

    /**
     * @return string
     */
    public function getPreviewImage();

    /**
     * @param string $previewImage
     *
     * @return VideoContent
     */
    public function setPreviewImage($previewImage);
}