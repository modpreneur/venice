<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * BlogArticleRepository.
 */
class BlogArticleRepository extends EntityRepository
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
              SELECT COUNT(article)
              FROM  VeniceAppBundle:BlogArticle AS article
              JOIN article.products products
              WHERE products.id = :productId
            ')
            ->setParameter('productId', $productId)
        ;

        return $query->getSingleScalarResult();
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
              SELECT COUNT(article)
              FROM  VeniceAppBundle:BlogArticle AS article
            ')
        ;

        return $query->getSingleScalarResult();
    }
}
