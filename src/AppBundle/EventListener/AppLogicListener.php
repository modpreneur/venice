<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 15:08
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\Product\Product;
use AppBundle\Entity\User;
use AppBundle\Event\FreeProductCreatedEvent;
use AppBundle\Event\NecktieLoginSuccessfulEvent;
use Doctrine\ORM\EntityManagerInterface;

class AppLogicListener
{
    /** @var  EntityManagerInterface */
    protected $entityManager;


    /**
     * FreeProductCreatedListener constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * Add access to all users
     *
     * @param FreeProductCreatedEvent $event
     */
    public function onFreeProductCreated(FreeProductCreatedEvent $event)
    {
        $this->giveLifetimeAccessToAllUsers($event->getProduct());
    }


    /**
     * Add access to all free products
     *
     * @param NecktieLoginSuccessfulEvent $event
     */
    public function onNecktieLoginSuccessful(NecktieLoginSuccessfulEvent $event)
    {
        $freeProducts = $this->entityManager->getRepository("AppBundle:Product\\FreeProduct")->findAll();

        foreach ($freeProducts as $freeProduct) {
            $this->giveLifetimeAccessToAllUsers($freeProduct);
        }
    }

    /**
     * Give lifetime access to all users
     *
     * @param Product $product
     */
    protected function giveLifetimeAccessToAllUsers(Product $product)
    {
        $users = $this->entityManager->getRepository("AppBundle:User")->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            $access = $user->giveAccessToProduct($product, new \DateTime("now"), null);
            if($access)
                $this->entityManager->persist($access);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();
    }
}