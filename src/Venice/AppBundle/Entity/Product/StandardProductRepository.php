<?php

namespace Venice\AppBundle\Entity\Product;

use Doctrine\ORM\EntityRepository;
use Trinity\NotificationBundle\Interfaces\NotificationEntityRepositoryInterface;

/**
 * StandardProductRepository
 */
class StandardProductRepository extends EntityRepository implements NotificationEntityRepositoryInterface
{
    /**
     * Select entity by id. Set fetch mode to "EAGER" to load all data.
     * todo: possible performance issue...
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
