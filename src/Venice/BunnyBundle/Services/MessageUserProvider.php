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

/**
 * Class MessageUserProvider.
 */
class MessageUserProvider implements MessageUserProviderInterface
{
    /** @var  TokenStorageInterface */
    protected $tokenStorage;

    /** @var  string */
    protected $clientIdentification;

    /**
     * MessageUserProvider constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param string                $clientIdentification
     */
    public function __construct(TokenStorageInterface $tokenStorage, string $clientIdentification)
    {
        $this->tokenStorage = $tokenStorage;
        $this->clientIdentification = $clientIdentification;
    }

    /**
     * Get user identification which sent the message.
     *
     * @param Message $message
     *
     * @return string
     */
    public function getUser(Message $message) : string
    {
        $token = $this->tokenStorage->getToken();

        if ($token !== null && $token->getUser() instanceof User) {
            return $token->getUser()->getNecktieId();
        }

        return $this->clientIdentification;
    }
}
