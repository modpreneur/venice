<?php

namespace Venice\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ContentProductRepository
 */
class ContentProductRepository extends EntityRepository
{
    /**
     * @param int $productId
     *
     * @return int
     */
    public function getCountByProduct(int $productId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(cp)
              FROM  VeniceAppBundle:ContentProduct AS cp
              WHERE cp.product.id == :productId
            ')
            ->setParameter('productId', $productId)
        ;
        return $query->getSingleScalarResult();
    }

    /**
     * @param int $contentId
     *
     * @return int
     */
    public function getCountByContent(int $contentId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(cp)
              FROM  VeniceAppBundle:ContentProduct AS cp
              WHERE cp.content.id == :contentId
            ')
            ->setParameter('contentId', $contentId)
        ;
        return $query->getSingleScalarResult();
    }
}
