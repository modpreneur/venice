<?php
namespace Venice\AppBundle\Entity\Interfaces;


/**
 * Class AbstractPlayable.
 */
interface AbstractPlayableContentInterface
{
    /**
     * @return int Duration(length) in seconds.
     */
    public function getDuration();

    /**
     * @param int $seconds Duration in seconds.
     *
     * @return $this
     */
    public function setDuration($seconds);
}