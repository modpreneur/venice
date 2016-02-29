<?php

namespace Venice\AppBundle\Entity\Product;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 */
class ProductRepository extends EntityRepository
{
    /**
     * Get One product by pay system id and itemId.
     *
     *
     * @return int
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
