<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 09.06.16
 * Time: 16:10.
 */
namespace Venice\BunnyBundle\Services;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Trinity\Bundle\MessagesBundle\Interfaces\MessageUserProviderInterface;
use Trinity\Bundle\MessagesBundle\Message\Message;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Services\EntityOverrideHandler;

/**
 * Class MessageUserProvider.
 */
class MessageUserProvider implements MessageUserProviderInterface
{
    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    /** @var  string */
    protected $clientIdentification;

    /** @var EntityOverrideHandler */
    protected $entityOverrideHandler;

    /**
     * MessageUserProvider constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param EntityOverrideHandler $entityOverrideHandler
     * @param string                $clientIdentification
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityOverrideHandler $entityOverrideHandler,
        string $clientIdentification
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->clientIdentification = $clientIdentification;
        $this->entityOverrideHandler = $entityOverrideHandler;
    }

    /**
     * Get user identification which sent the message.
     *
     * @param Message $message
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getUser(Message $message) : string
    {
        $token = $this->tokenStorage->getToken();

        if ($token !== null && $this->entityOverrideHandler->isInstanceOf($token->getUser(), User::class)) {
            return $token->getUser()->getNecktieId();
        }

        return $this->clientIdentification;
    }
}
