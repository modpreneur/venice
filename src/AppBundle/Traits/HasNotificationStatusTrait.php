<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 06.01.16
 * Time: 16:48
 */

namespace AppBundle\Traits;


use Trinity\FrameworkBundle\Entity\ClientInterface;

/**
 * Trait HasNotificationStateTrait
 *
 * Don not forget to initialize $status to empty array in constructor.
 */
trait HasNotificationStatusTrait
{
    /**
     * @var []
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $notificationStatus;


    /**
     * @param ClientInterface $client
     * @param string $status
     * @return void
     */
    public function setNotificationStatus(ClientInterface $client, $status)
    {
        $this->notificationStatus[$client->getId()] = $status;
    }


    /**
     * @return array|null
     */
    public function getNotificationStatus()
    {
        return $this->notificationStatus;
    }
}