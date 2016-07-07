<?php

namespace Venice\AppBundle\Entity\Content;

use Doctrine\ORM\EntityRepository;

/**
 * ContentRepository
 */
class ContentRepository extends EntityRepository
{
    /**
     * @param int $productId
     *
     * @return int
     */
    public function getCountByProduct(int $productId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(content)
              FROM  VeniceAppBundle:Content\Content AS content
              WHERE content.product.id == :productId
            ')
            ->setParameter('productId', $productId)
        ;
        return $query->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function count()
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(content)
              FROM  VeniceAppBundle:Content\Content AS content
            ')
        ;
        return $query->getSingleScalarResult();
    }
}
