<?php

namespace Venice\AppBundle\Services;

use Trinity\NotificationBundle\Entity\Notification;
use Trinity\NotificationBundle\Entity\NotificationStatus;
use Trinity\NotificationBundle\Interfaces\NotificationLoggerInterface;

/**
 * Class NotificationLogger
 */
class NotificationLogger implements NotificationLoggerInterface
{

    /**
     * @param Notification $notification
     */
    public function logIncomingNotification(Notification $notification)
    {
        // TODO: Implement logIncomingNotification() method.
    }

    /**
     * @param Notification $notification
     */
    public function logOutcomingNotification(Notification $notification)
    {
        // TODO: Implement logOutcomingNotification() method.
    }

    /**
     * Set status of the notifications
     *
     * @param NotificationStatus[] $statuses
     */
    public function setNotificationStatuses(array $statuses)
    {
        // TODO: Implement setNotificationStatuses() method.
    }
}