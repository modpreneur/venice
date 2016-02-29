<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 16:13
 */

namespace Venice\AppBundle\Event;


use Venice\AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class NecktieLoginSuccessfulEvent extends Event
{
    /** @var User */
    protected $user;

    /**
     * FreeProductCreatedEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user= $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}