<?php

namespace Venice\AppBundle\Services;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Trinity\FrameworkBundle\Services\TrinityFormCreator;

/**
 * Class FormCreator
 * @package Venice\AppBundle\Services
 */
class FormCreator extends TrinityFormCreator
{
    /**
     * FormFactory constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router)
    {
        parent::__construct($formFactory, $router);

        $this->formFactory = $formFactory;
        $this->router = $router;
    }
}
