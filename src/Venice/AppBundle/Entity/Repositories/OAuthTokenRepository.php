<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * OAuthTokenRepository
 */
class OAuthTokenRepository extends EntityRepository
{
    /**
     * @return int
     */
    public function count()
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(t)
              FROM  VeniceAppBundle:OAuthToken AS t
            ');

        return $query->getSingleScalarResult();
    }
}
