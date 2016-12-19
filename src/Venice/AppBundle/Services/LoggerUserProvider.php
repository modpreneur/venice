<?php

namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Trinity\Bundle\LoggerBundle\Interfaces\UserProviderInterface;
use Venice\AppBundle\Entity\User;

/**
 * {@inheritDoc}
 */
class LoggerUserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EntityOverrideHandler
     */
    protected $overrideHandler;

    /**
     * LoggerUserProvider constructor.
     * @param EntityManagerInterface $entityManager
     * @param EntityOverrideHandler $overrideHandler
     */
    public function __construct(EntityManagerInterface $entityManager, EntityOverrideHandler $overrideHandler)
    {
        $this->entityManager = $entityManager;
        $this->overrideHandler = $overrideHandler;
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
        $class = $this->overrideHandler->getEntityClass(User::class);

        return $this->entityManager->getRepository($class)->find($userId);
    }
}