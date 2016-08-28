<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository.
 */
class ProductRepository extends EntityRepository
{
    /**
     * @see http://jayroman.com/blog/symfony2-quirks-with-doctrine-inheritance-and-unique-constraints
     *
     * @param string[] $criteria format: array('user' => <user_id>, 'name' => <name>)
     *
     * @return array|\Venice\AppBundle\Entity\Content\Content[]|\Venice\AppBundle\Entity\Product\Product[]
     */
    public function findByUniqueCriteria(array $criteria)
    {
        /*
         * The findByName method must explicitly query the main entity,
         * otherwise you will check a the uniqueness only for that type (name = ? AND type = ?)
         */
        return $this->getEntityManager()->getRepository('VeniceAppBundle:Product\Product')->findBy($criteria);
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
              SELECT COUNT(product)
              FROM  VeniceAppBundle:Product\Product AS product
            ')
        ;

        return $query->getSingleScalarResult();
    }
}