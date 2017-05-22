<?php

namespace Venice\AppBundle\Services;

use Trinity\Bundle\LoggerBundle\Interfaces\LoggerTtlProviderInterface;
use Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException;
use Trinity\Bundle\SettingsBundle\Manager\SettingsManager;


/**
 * Class LoggerTtlProvider
 */
class LoggerTtlProvider implements LoggerTtlProviderInterface
{
    /** @var  SettingsManager */
    protected $settingsManager;

    /**
     * LoggerTtlProvider constructor.
     * @param SettingsManager $settingsManager
     */
    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * Get ttl in days for the given type.
     *
     * @param string $typeName Name of the elasticlog type
     *
     * @return int Ttl in days. 0(zero) stands for no ttl.
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws PropertyNotExistsException
     */
    public function getTtlForType(string $typeName): int
    {
        //you can set different ttl for each typeName, but for now let us KISS
        return $this->settingsManager->get('logger_ttl');
    }
}