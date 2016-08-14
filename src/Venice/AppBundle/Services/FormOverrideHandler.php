<?php

namespace Venice\AppBundle\Services;

use Venice\AppBundle\Interfaces\ConfiguratorInterface;

/**
 * This class is made to be used to simplify the work with forms.
 * It checks if the given form was overridden and if so it returns the overridden form.
 *
 * Class FormOverrideHandler
 */
class FormOverrideHandler
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
     * @param string $formClass
     *
     * @return mixed|string
     */
    public function getFormClass(string $formClass)
    {
        return $this->configurator->getValueFromKey($formClass, $formClass);
    }
}
