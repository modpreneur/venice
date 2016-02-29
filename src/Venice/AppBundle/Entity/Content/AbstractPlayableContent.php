<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 12:51
 */

namespace Venice\AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class AbstractPlayable
 * @package Venice\AppBundle\Entity\Abstracts
 */
abstract class AbstractPlayableContent extends Content
{
    /**
     * @var int Duration(length) in seconds.
     *
     * @Assert\Range(min=1)
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