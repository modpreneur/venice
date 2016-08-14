<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * ContentProductRepository.
 */
class ContentProductRepository extends EntityRepository
{
    /**
     * @param int $productId
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountByProduct(int $productId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(cp)
              FROM  VeniceAppBundle:ContentProduct AS cp
              JOIN cp.product product
              WHERE product.id = :productId
            ')
            ->setParameter('productId', $productId)
        ;

        return $query->getSingleScalarResult();
    }

    /**
     * @param int $contentId
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountByContent(int $contentId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(cp)
              FROM  VeniceAppBundle:ContentProduct AS cp
              JOIN cp.content content
              WHERE content.id = :contentId
            ')
            ->setParameter('contentId', $contentId)
        ;

        return $query->getSingleScalarResult();
    }
}
