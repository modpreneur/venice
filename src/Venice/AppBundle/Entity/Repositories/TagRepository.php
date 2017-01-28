<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository.
 */
class TagRepository extends EntityRepository
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
              SELECT COUNT(tag)
              FROM  VeniceAppBundle:Tag AS tag
            ')
        ;

        return $query->getSingleScalarResult();
    }
}
