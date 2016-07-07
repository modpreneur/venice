<?php

namespace Venice\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BlogArticleRepository
 *
 */
class BlogArticleRepository extends EntityRepository
{
    /**
     * @param int $productId
     *
     * @return int
     */
    public function getCountByProduct(int $productId)
    {
        $query = $this->getEntityManager()->createQuery('
              SELECT COUNT(article)
              FROM  VeniceAppBundle:BlogArticle AS article
              WHERE article.product.id == :productId
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
              SELECT COUNT(article)
              FROM  VeniceAppBundle:BlogArticle AS article
            ')
        ;
        return $query->getSingleScalarResult();
    }
}
