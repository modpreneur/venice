<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Interfaces\ConfiguratorInterface;

class EntityOverrideHandler
{
    /** @var ConfiguratorInterface */
    protected $configurator;

    /**
     * FormOverrideHandler constructor.
     * @param $configurator
     */
    public function __construct(ConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
    }

    /**
     * @param string $entityClass
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
     * @param array $constructorArgs
     * @return object
     */
    public function getEntityInstance(string $entityClass, array $constructorArgs = [])
    {
        $class = $this->getEntityClass($entityClass);

        if (count($constructorArgs) < 1) {
            return new $class;
        } else {
            $reflection = new \ReflectionClass($class);

            return $reflection->newInstanceArgs($constructorArgs);
        }


    }
}