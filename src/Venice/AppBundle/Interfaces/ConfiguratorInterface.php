<?php

namespace Venice\AppBundle\Interfaces;

interface ConfiguratorInterface
{
    /**
     * ConfiguratorInterface constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration = []);

    /**
     * @return array
     */
    public function getConfiguration() : array;

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @param $key
     * @param null $default
     * @return string|mixed
     */
    public function getValueFromKey($key, $default = null);

    /**
     * @param $value
     * @param null $default
     * @return mixed
     */
    public function getKeyFromValue($value, $default = null);
}
