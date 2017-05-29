<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;

/**
 * Class UserAccessService
 *
 * Originally in the User entity. Refactored to separate class as it was making an instance of ProductAccess.
 */
class UserAccessService
{
    /**
     * @var EntityOverrideHandler
     */
    protected $entityOverrideHandler;

    /**
     * UserAccessService constructor.
     * @param EntityOverrideHandler $entityOverrideHandler
     */
    public function __construct(EntityOverrideHandler $entityOverrideHandler)
    {
        $this->entityOverrideHandler = $entityOverrideHandler;
    }

    /**
     * @param User $user
     * @param Product $product
     * @param \DateTime $fromDate
     * @param \DateTime|null $toDate
     * @param int|null $necktieId
     * @return null|object|ProductAccess
     */
    public function giveAccessToProduct(
        User $user,
        Product $product,
        \DateTime $fromDate,
        \DateTime $toDate = null,
        ?int $necktieId = null
    ) {
        $productAccess = $user->getProductAccess($product);
        $now = new \DateTime('now');

        // If the user does not have an access - 3 situations:
        // 1st - the productAccess between this user and product does not exist
        // 2nd - the productAccess between this user and product exists but it has already expired(toDate < now)
        // 3rd - the productAccess between this user and product exists but the fromDate > now

        // 1st - create a new ProductAccess entity
        if (!$productAccess) {
            $productAccess = $this->entityOverrideHandler->getEntityInstance(ProductAccess::class);
            $productAccess
                ->setNecktieId($necktieId)
                ->setProduct($product)
                ->setUser($user)
                ->setFromDate($fromDate)
                ->setToDate($toDate);

            $user->addProductAccess($productAccess);

            return $productAccess;
        } // 2nd - set the toDate property to given toDate
        elseif ($productAccess->getToDate() !== null && $productAccess->getToDate() < $now) {
            $productAccess->setToDate($toDate);
        } //3rd - set the fromDate property to given fromDate
        elseif ($productAccess->getFromDate() > $now) {
            $productAccess->setFromDate($fromDate);
        }

        return $productAccess;
    }
}