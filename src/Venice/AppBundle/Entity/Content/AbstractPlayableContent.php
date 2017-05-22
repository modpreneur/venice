<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:51.
 */

namespace Venice\AppBundle\Entity\Content;

/**
 * Class AbstractPlayable.
 */
abstract class AbstractPlayableContent extends Content
{
    /**
     * @var int Duration(length) in seconds.
     *
     */
    protected $duration;

    /**
     * @return int Duration(length) in seconds.
     */
    abstract public function getDuration();

    /**
     * @param int $seconds Duration in seconds.
     *
     * @return $this
     */
    abstract public function setDuration($seconds);
}
