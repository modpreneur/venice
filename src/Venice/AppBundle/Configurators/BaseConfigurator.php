<?php

namespace Venice\AppBundle\Configurators;

use Venice\AppBundle\Interfaces\ConfiguratorInterface;

class BaseConfigurator implements ConfiguratorInterface
{
    protected $configuration;

    /**
     * ConfiguratorInterface constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = $configuration;
    }

    /**
     * @return array
     */
    public function getConfiguration() : array
    {
        return $this->configuration;
    }

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return string|mixed
     */
    public function getValueFromKey($key, $default = null)
    {
        if (array_key_exists($key, $this->configuration)) {
            return $this->configuration[$key];
        } else {
            return $default;
        }
    }

    /**
     * @param $value
     * @param null $default
     *
     * @return mixed|string
     */
    public function getKeyFromValue($value, $default = null)
    {
        $result = array_search($value, $this->configuration, true);

        if (false === $result) {
            return $default;
        } else {
            return $result;
        }
    }
}
