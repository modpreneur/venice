<?php

namespace Venice\AppBundle\Services;

use Trinity\Component\Core\Interfaces\EntityInterface;
use Venice\AppBundle\Interfaces\ConfiguratorInterface;

/**
 * Class EntityOverrideHandler.
 *
 * todo: refactor to factory and split to 2 classes? EntityFactory and
 */
class EntityOverrideHandler
{
    /** @var ConfiguratorInterface */
    protected $configurator;

    /**
     * FormOverrideHandler constructor.
     *
     * @param $configurator
     */
    public function __construct(ConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @param string $entityClass
     *
     * @return mixed|string
     */
    public function getEntityClass(string $entityClass)
    {
        return $this->configurator->getValueFromKey($entityClass, $entityClass);
    }

    /**
     * Get instance of the given entity class.
     *
     * @param string $entityClass
     * @param array  $constructorArgs
     *
     * @return EntityInterface
     */
    public function getEntityInstance(string $entityClass, array $constructorArgs = []): EntityInterface
    {
        $class = $this->getEntityClass($entityClass);

        if (count($constructorArgs) < 1) {
            return new $class();
        } else {
            $reflection = new \ReflectionClass($class);

            return $reflection->newInstanceArgs($constructorArgs);
        }
    }

    /**
     * Check if given $entity is instance of $class.
     * You can either use this method or use the instanceof operator with the entity interface.
     *
     * @param object|string $entity
     * @param string        $class
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function isInstanceOf($entity, string $class)
    {
        if (is_object($entity)) {
            return get_class($entity) === $this->getEntityClass($class);
        } elseif (is_string($entity)) {
            return $entity === $this->getEntityClass($class);
        } else {
            throw new \InvalidArgumentException(
                'The argument "entity" has to be an object or a string, '.gettype($entity).' given'
            );
        }
    }
}
