<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Venice\AppBundle\Entity\Interfaces\ContentInterface;

/**
 * ContentRepository.
 */
class ContentRepository extends EntityRepository
{
    /**
     * @see http://jayroman.com/blog/symfony2-quirks-with-doctrine-inheritance-and-unique-constraints
     *
     * @param string[] $criteria format: array('user' => <user_id>, 'name' => <name>)
     *
     * @return array|ContentInterface[]
     */
    public function findByUniqueCriteria(array $criteria)
    {
        /*
         * The findByName method must explicitly query the main entity,
         * otherwise you will check a the uniqueness only for that type (name = ? AND type = ?)
         */
        return $this->getEntityManager()->getRepository('VeniceAppBundle:Content\Content')->findBy($criteria);
    }

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
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
