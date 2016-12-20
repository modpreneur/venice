<?php

namespace Venice\AppBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Trinity\Bundle\LoggerBundle\Interfaces\UserProviderInterface;
use Venice\AppBundle\Entity\User;

/**
 * {@inheritDoc}
 */
class LoggerUserProvider implements UserProviderInterface
{
    /**
     * @var Registry
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
    public function __construct(Registry $doctrine, EntityOverrideHandler $overrideHandler)
    {
        $this->doctrine = $doctrine;
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

        return $this->doctrine->getManager()->getRepository($class)->find($userId);
    }
}