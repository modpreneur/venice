<?php

namespace Venice\AppBundle\Entity\Repositories;

use Doctrine\ORM\EntityRepository;
use Trinity\NotificationBundle\Interfaces\NotificationEntityRepositoryInterface;
use Venice\AppBundle\Entity\Product\Product;

/**
 * StandardProductRepository
 */
class StandardProductRepository extends ProductRepository implements NotificationEntityRepositoryInterface
{
    /**
     * Select entity by id. Set fetch mode to "EAGER" to load all data.
     *
     * @param $id
     *
     * @return Product|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findEagerly($id)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT product
            FROM VeniceAppBundle:Product\StandardProduct AS product
            WHERE product.id = :id
        ')
            ->setParameter('id', $id);

        return $query->getOneOrNullResult();
    }
}
