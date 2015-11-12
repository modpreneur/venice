<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:51
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractPlayable
 * @package AppBundle\Entity\Abstracts
 */
abstract class AbstractPlayableContent extends Content
{
    /**
     * @var int Duration(length) in seconds.
     *
     * @ORM\Column(name="duration", type="integer", nullable=false)
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