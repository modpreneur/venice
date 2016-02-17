<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.02.16
 * Time: 16:21
 */

namespace AppBundle\EventListener;


use AppBundle\Event\NecktieLoginSuccessfulEvent;

class LoginListener
{
    public function onSuccessfulLogin(NecktieLoginSuccessfulEvent $event)
    {
        $user = $event->getUser();
        $user->setLastLogin(new \DateTime("now"));
    }
}