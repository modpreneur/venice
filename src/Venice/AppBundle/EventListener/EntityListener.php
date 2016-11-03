<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 18.01.16
 * Time: 16:21.
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
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function processChanges(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $uow = $entityManager->getUnitOfWork();

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
     * Check validation on entity.
     *
     * @param $entity
     *
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    protected function checkViolations($entity)
    {
        $violations = $this->container->get('validator')->validate($entity);
        $message = '';

        if ($violations->count() !== 0) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $invalidValue = $violation->getInvalidValue();

                if ($invalidValue instanceof \DateTime) {
                    $invalidValue = $invalidValue->format(\DateTime::W3C);
                }

                //todo: convert datetime to string
                $message .= 'Validation failed for entity: '.get_class($entity).
                    ' at property: '.$violation->getPropertyPath().': '.
                    $violation->getMessage().'The value is:'.$invalidValue
                ;
            }

            throw new ValidatorException($message);
        }
    }
}
