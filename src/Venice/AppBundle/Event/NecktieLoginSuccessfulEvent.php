<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 16:13.
 */
namespace Venice\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Venice\AppBundle\Entity\Interfaces\UserInterface;

/**
 * {@inheritdoc}
 */
class NecktieLoginSuccessfulEvent extends Event
{
    /** @var UserInterface */
    protected $user;

    /**
     * NecktieLoginSuccessfulEvent constructor.
     *
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
