<?php

namespace Venice\AppBundle\Entity\Repositories;

use Trinity\NotificationBundle\Interfaces\NotificationEntityRepositoryInterface;
use Venice\AppBundle\Entity\Interfaces\ContentInterface;
use Venice\AppBundle\Entity\Interfaces\ProductInterface;
use Venice\AppBundle\Entity\Interfaces\StandardProductInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * StandardProductRepository.
 */
class StandardProductRepository extends ProductRepository implements NotificationEntityRepositoryInterface
{
    /**
     * @see http://jayroman.com/blog/symfony2-quirks-with-doctrine-inheritance-and-unique-constraints
     *
     * @param string[] $criteria format: array('user' => <user_id>, 'name' => <name>)
     *
     * @return array|ContentInterface[]|ProductInterface[]|StandardProductInterface[]
     */
    public function findByUniqueCriteria(array $criteria)
    {
        /*
         * The findByName method must explicitly query the main entity,
         * otherwise you will check a the uniqueness only for that type (name = ? AND type = ?)
         */
        return $this->getEntityManager()->getRepository(StandardProduct::class)->findBy($criteria);
    }

    /**
     * Select entity by id. Set fetch mode to "EAGER" to load all data.
     *
     * @param $id
     *
     * @return ProductInterface|null
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
