<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Trinity\NotificationBundle\Interfaces\NotificationEntityRepositoryInterface;

/**
 * ProductAccessRepository
 */
class ProductAccessRepository extends EntityRepository implements NotificationEntityRepositoryInterface
{
    /**
     * Select entity by id. Set fetch mode to "EAGER" to load all data.
     *
     * @param $id
     *
     * @return BillingPlan|null
     *
     * @throws NonUniqueResultException
     */
    public function findEagerly($id)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT pa
            FROM VeniceAppBundle:ProductAccess AS pa
            WHERE pa.id = :id
        ')
            ->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }

    /**
     * @param $userId
     *
     * @return int
     */
    public function count($userId)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT COUNT(pa)
            FROM VeniceAppBundle:ProductAccess AS pa
            WHERE pa.user = :userId
        ')
            ->setParameter('userId', $userId);

        return $query->getSingleScalarResult();
    }
}
