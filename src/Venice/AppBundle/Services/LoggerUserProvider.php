<?php

namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Trinity\Bundle\LoggerBundle\Interfaces\UserProviderInterface;
use Trinity\Component\Core\Interfaces\UserInterface;
use Venice\AppBundle\Entity\User;

/**
 * Class LoggerUserProvider
 */
class LoggerUserProvider implements UserProviderInterface
{
    /** @var  RegistryInterface */
    protected $doctrineRegistry;

    /** @var  EntityOverrideHandler */
    protected $entityOverrideHandler;

    /**
     * UserProvider constructor.
     *
     * @param RegistryInterface $registry
     * @param EntityOverrideHandler $entityOverrideHandler
     */
    public function __construct(RegistryInterface $registry, EntityOverrideHandler $entityOverrideHandler)
    {
        $this->doctrineRegistry = $registry;
        $this->entityOverrideHandler = $entityOverrideHandler;
    }

    /**
     * Get user by id.
     *
     * @param int $userId
     *
     * @return \Trinity\Component\Core\Interfaces\UserInterface
     */
    public function getUserById(int $userId)
    {
        /** @var UserInterface $user */
        $user = $this->doctrineRegistry->getEntityManager()->getRepository(
            $this->entityOverrideHandler->getEntityClass(User::class)
        )->find($userId);

        return $user;
    }
}