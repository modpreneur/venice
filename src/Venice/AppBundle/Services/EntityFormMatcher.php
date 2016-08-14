<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Interfaces\ConfiguratorInterface;

/**
 * Class EntityFormMatcher.
 *
 * Gets the appropriate Form(Type) for the given entity
 */
class EntityFormMatcher
{
    /** @var  ConfiguratorInterface */
    protected $formConfigurator;

    /**
     * EntityFormMatcher constructor.
     *
     * @param ConfiguratorInterface $configurator
     */
    public function __construct(ConfiguratorInterface $configurator)
    {
        $this->formConfigurator = $configurator;
    }

    /**
     * Get the appropriate form for given entity.
     *
     * @param object|string $entity
     *
     * @return string
     *
     * @throws \LogicException
     */
    public function getFormClassForEntity($entity)
    {
        if (is_object($entity)) {
            $entityClass = $this->getEntityClass($entity);
        } elseif (class_exists($entity)) {
            $entityClass = $entity;
        } else {
            throw new \InvalidArgumentException('The given argument is not valid class name nor object');
        }

        // Get form class from the configuration.
        $entityForm = $this->formConfigurator->getValueFromKey($entityClass, null);
        if (null !== $entityForm) {
            return $entityForm;
        } else {
            throw new \LogicException("Could not find a form for the $entityClass class");
        }
    }

    /**
     * Get entity class with the fixed doctrine proxy namespace.
     *
     * @param object $entity
     *
     * @return string
     */
    protected function getEntityClass($entity) : string
    {
        //remove namespace prefix in case of the entity is Doctrine proxy
        return str_replace('Proxies\\__CG__\\', '', get_class($entity));
    }
}
