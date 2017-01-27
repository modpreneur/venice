<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * CategoryRepository.
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count()
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(category)
              FROM  VeniceAppBundle:Category AS category
            ')
        ;

        return $query->getSingleScalarResult();
    }
}
