<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 16:21
 */

namespace Venice\AppBundle\EventListener;


use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class EntityListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function onFlush(OnFlushEventArgs $args)
    {
        $this->processChanges($args);
    }


    /**
     * @param OnFlushEventArgs $args
     */
    public function processChanges(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->checkViolations($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->checkViolations($entity);
        }

        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            foreach ($col as $entity) {
                $this->checkViolations($entity);
            }
        }
    }

    /**
     * Check validation on entity
     *
     * @param $entity
     */
    protected function checkViolations($entity)
    {
        $violations = $this->container->get("validator")->validate($entity);
        $message = "";

        if ($violations->count() !== 0) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $message .= $violation->getPropertyPath().": ".$violation->getMessage().";";
            }

            throw new ValidatorException($message);
        }
    }
}
