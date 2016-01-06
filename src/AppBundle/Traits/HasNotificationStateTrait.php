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
trait HasNotificationStateTrait
{
    /**
     * @var []
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $status;


    /** @return ClientInterface[] */
    public function getClients()
    {
        return [];
    }

    /**
     * @param ClientInterface $client
     * @param string $status
     * @return void
     */
    public function setSyncStatus(ClientInterface $client, $status)
    {
        $this->status[$client->getId()] = $status;
    }
}