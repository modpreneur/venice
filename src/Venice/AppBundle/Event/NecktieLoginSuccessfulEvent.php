<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 16:13.
 */
namespace Venice\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Venice\AppBundle\Entity\User;

/**
 * {@inheritdoc}
 */
class NecktieLoginSuccessfulEvent extends Event
{
    /** @var User */
    protected $user;

    /**
     * NecktieLoginSuccessfulEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
