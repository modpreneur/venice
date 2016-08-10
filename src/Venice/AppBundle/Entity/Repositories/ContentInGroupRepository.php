<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * ContentInGroupRepository
 */
class ContentInGroupRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function count()
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(cig)
              FROM  VeniceAppBundle:ContentInGroup AS cig
            ');

        return $query->getSingleScalarResult();
    }
}
