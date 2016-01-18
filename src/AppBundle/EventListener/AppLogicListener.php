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
use Symfony\Bridge\Doctrine\RegistryInterface;

class AppLogicListener
{
    /** @var  RegistryInterface */
    protected $registry;


    /**
     * FreeProductCreatedListener constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
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
        $freeProducts = $this->getEntityManager()->getRepository("AppBundle:Product\\FreeProduct")->findAll();
        $user = $event->getUser();

        foreach ($freeProducts as $product) {
            if(!$user->hasAccessToProduct($product)){
                $user->giveAccessToProduct($product, new \DateTime("now"));
            }
        }

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
        $users = $this->getEntityManager()->getRepository("AppBundle:User")->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            $access = $user->giveAccessToProduct($product, new \DateTime("now"), null);
            if($access)
                $this->getEntityManager()->persist($access);
            $this->getEntityManager()->persist($user);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        return $this->registry->getManager();
    }
}