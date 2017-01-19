<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Trinity\NotificationBundle\Interfaces\NotificationEntityRepositoryInterface;
use Venice\AppBundle\Entity\Interfaces\UserInterface;

/**
 * UserRepository.
 */
class UserRepository extends EntityRepository implements NotificationEntityRepositoryInterface
{
    /**
     * Select entity by id. Set fetch mode to "EAGER" to load all data.
     *
     * @param $id
     *
     * @return UserInterface|null
     *
     * @throws NonUniqueResultException
     */
    public function findEagerly($id)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT user
            FROM VeniceAppBundle:User AS user
            WHERE user.id = :id
        ')
            ->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    /**
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count()
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(user)
              FROM  VeniceAppBundle:User AS user
            ')
        ;

        return $query->getSingleScalarResult();
    }
}
