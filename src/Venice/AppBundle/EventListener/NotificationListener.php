<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.05.16
 * Time: 21:20.
 */
namespace Venice\AppBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Trinity\Bundle\MessagesBundle\Event\ReadMessageEvent;
use Trinity\Component\Utils\Services\PriceStringGenerator;
use Trinity\NotificationBundle\Entity\SynchronizationStoppedMessage;
use Trinity\NotificationBundle\Event\BeforeDeleteEntityEvent;
use Trinity\NotificationBundle\Event\ChangesDoneEvent;
use Trinity\NotificationBundle\Services\EntityAliasTranslator;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Services\EntityOverrideHandler;

/**
 * Class NotificationListener.
 */
class NotificationListener
{
    /** @var  PriceStringGenerator */
    protected $priceStringGenerator;

    /** @var  EntityManagerInterface */
    protected $entityManager;

    /** @var  EntityAliasTranslator */
    protected $entityAliasTranslator;

    /** @var  EntityOverrideHandler */
    protected $entityOverrideHandler;

    /** @var  LoggerInterface */
    protected $logger;

    /**
     * NotificationListener constructor.
     *
     * @param PriceStringGenerator   $priceStringGenerator
     * @param EntityManagerInterface $entityManager
     * @param EntityAliasTranslator  $entityAliasTranslator
     * @param EntityOverrideHandler  $entityOverrideHandler
     * @param LoggerInterface        $logger
     */
    public function __construct(
        PriceStringGenerator $priceStringGenerator,
        EntityManagerInterface $entityManager,
        EntityAliasTranslator $entityAliasTranslator,
        EntityOverrideHandler $entityOverrideHandler,
        LoggerInterface $logger
    ) {
        $this->priceStringGenerator = $priceStringGenerator;
        $this->entityManager = $entityManager;
        $this->entityAliasTranslator = $entityAliasTranslator;
        $this->entityOverrideHandler = $entityOverrideHandler;
        $this->logger = $logger;
    }

    /**
     * @param ChangesDoneEvent $event
     *
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     * @throws \InvalidArgumentException
     */
    public function onChangesDone(ChangesDoneEvent $event)
    {
        foreach ($event->getEntities() as $entity) {
            if ($this->entityOverrideHandler->isInstanceOf($entity, BillingPlan::class)) {
                /* @var $entity BillingPlan */
                $entity->setPrice($this->priceStringGenerator->generateFullPriceStr($entity));
            } elseif ($this->entityOverrideHandler->isInstanceOf($entity, StandardProduct::class)) {
                /* @var $entity StandardProduct */
                $entity->setPurchasable(true);
            }

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    /**
     * @param BeforeDeleteEntityEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onBeforeDeleteEntity(BeforeDeleteEntityEvent $event)
    {
        $entity = $event->getEntity();

        if ($this->entityOverrideHandler->isInstanceOf($entity, BillingPlan::class)) {
            /* @var $entity BillingPlan */
            $entity->setProduct(null);
        }
    }

    /**
     * @param ReadMessageEvent $event
     *
     * @throws \Trinity\NotificationBundle\Exception\EntityAliasNotFoundException
     * @throws \InvalidArgumentException
     */
    public function onMessageRead(ReadMessageEvent $event)
    {
        $message = $event->getMessage();
        if ($message->getType() === SynchronizationStoppedMessage::MESSAGE_TYPE) {
            $this->handleSynchronizationStoppedMessage(SynchronizationStoppedMessage::createFromMessage($message));
            $event->stopPropagation();
            $event->setEventProcessed(true);
        }
    }

    /**
     * @param SynchronizationStoppedMessage $message
     *
     * @throws \Trinity\NotificationBundle\Exception\EntityAliasNotFoundException
     * @throws \InvalidArgumentException
     */
    protected function handleSynchronizationStoppedMessage(SynchronizationStoppedMessage $message)
    {
        $entityClass = $this->entityAliasTranslator->getClassFromAlias($message->getEntityAlias());
        $entityId = $message->getEntityId();

        if ($this->entityOverrideHandler->isInstanceOf($entityClass, StandardProduct::class)) {
            $this->logger->info('Read SynchronizationStoppedMessage about product '.$entityClass.' with id:'.$entityId);
            /** @var StandardProduct $product */
            $product = $this->entityManager->find($entityClass, $entityId);
            $product->setPurchasable(false);
            $this->persistEntity($product);
        } else {
            $this->logger->error('Read SynchronizationStoppedMessage about entity '.$entityClass.' with id:'.$entityId);
        }
    }

    /**
     * @param $entity
     */
    protected function persistEntity($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
