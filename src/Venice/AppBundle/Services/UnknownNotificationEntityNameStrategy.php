<?php

namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Trinity\NotificationBundle\Entity\Notification;
use Trinity\NotificationBundle\Interfaces\UnknownEntityNameStrategyInterface;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Interfaces\PaySystemVendorInterface;
use Venice\AppBundle\Entity\Interfaces\StandardProductInterface;
use Venice\AppBundle\Entity\PaySystemVendor;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class UnknownNotificationEntityNameStrategy
 */
class UnknownNotificationEntityNameStrategy implements UnknownEntityNameStrategyInterface
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EntityOverrideHandler */
    protected $entityOverrideHandler;

    /**
     * UnknownNotificationEntityNameStrategy constructor.
     * @param EntityManagerInterface $entityManager
     * @param EntityOverrideHandler $entityOverrideHandler
     */
    public function __construct(EntityManagerInterface $entityManager, EntityOverrideHandler $entityOverrideHandler)
    {
        $this->entityManager = $entityManager;
        $this->entityOverrideHandler = $entityOverrideHandler;
    }

    /**
     * This method is called when the system receives a notification with entity name which is not known.
     * This handler could for example perform some custom actions or throw an exception.
     *
     * @param Notification $notification The notification which has the unknown entity name
     *
     * @return bool True if the solver solved the situation. False otherwise
     * @throws \InvalidArgumentException
     */
    public function unknownEntityName(Notification $notification)
    {
        // we will receive a notification about default billing plans
        if ($notification->getEntityName() !== 'default-billing-plans') {
            return false;
        }

        $data = $notification->getData();

        foreach (['id', 'product', 'paySystemVendor', 'defaultBillingPlan'] as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \InvalidArgumentException('The data array does not contain key: ' . $key);
            }
        }

        /** @var PaySystemVendorInterface $vendor */
        $vendor = $this->entityManager->getRepository(
            $this->entityOverrideHandler->getEntityClass(PaySystemVendor::class)
        )->findOneBy(['necktieId' => $data['paySystemVendor']]);

        if ($vendor !== null && $vendor->isDefaultForVenice()) {
            // change the default billing plan for the product
            /** @var StandardProductInterface $product */
            $product = $this->entityManager->getRepository(
                $this->entityOverrideHandler->getEntityClass(StandardProduct::class)
            )->findOneBy(['necktieId' => $data['product']]);

            /** @var BillingPlan $billingPlan */
            $billingPlan = $this->entityManager->getRepository(
                $this->entityOverrideHandler->getEntityClass(BillingPlan::class)
            )->findOneBy(['necktieId' => $data['defaultBillingPlan']]);

            if ($product !== null && $billingPlan !== null) {
                $product->setNecktieDefaultBillingPlan($billingPlan);

                $this->entityManager->persist($product);
                $this->entityManager->flush();
            }
        }

        return true;
    }
}
