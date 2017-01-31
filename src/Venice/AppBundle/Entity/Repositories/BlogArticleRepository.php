<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Venice\AppBundle\Entity\Category;

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
     * @param Category $category
     *
     * @return int
     */
    public function getCountByCategory(Category $category)
    {
        $query = $this->getEntityManager()->createQuery('
                  SELECT COUNT(article)
                  FROM  VeniceAppBundle:BlogArticle AS article
                  WHERE :category MEMBER OF article.categories
                ')
            ->setParameter('category', $category)
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
