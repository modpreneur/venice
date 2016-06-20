<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.05.16
 * Time: 21:20
 */

namespace Venice\AppBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Trinity\FrameworkBundle\Services\PriceStringGenerator;
use Trinity\NotificationBundle\Event\BeforeDeleteEntityEvent;
use Trinity\NotificationBundle\Event\ChangesDoneEvent;
use Venice\AppBundle\Entity\BillingPlan;

class NotificationListener
{
    /** @var  PriceStringGenerator */
    protected $priceStringGenerator;

    /** @var  EntityManagerInterface */
    protected $entityManager;

    /**
     * NotificationListener constructor.
     *
     * @param PriceStringGenerator   $priceStringGenerator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PriceStringGenerator $priceStringGenerator, EntityManagerInterface $entityManager)
    {
        $this->priceStringGenerator = $priceStringGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param ChangesDoneEvent $event
     */
    public function onChangesDone(ChangesDoneEvent $event)
    {
        foreach ($event->getEntities() as $entity) {
            if ($entity instanceof BillingPlan) {
                $entity->setPrice($this->priceStringGenerator->generateFullPriceStr($entity));
            }

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param BeforeDeleteEntityEvent $event
     */
    public function onBeforeDeleteEntity(BeforeDeleteEntityEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof BillingPlan) {
            /** @var $entity BillingPlan */
            $entity->setProduct(null);
        }
    }
}