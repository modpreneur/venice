<?php

namespace Venice\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This is just a sandbox controller.
 *
 * Class DefaultController
 */
class DefaultController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function indexAction()
    {
        return $this->render('VeniceAppBundle:default:index.html.twig');
    }
}
