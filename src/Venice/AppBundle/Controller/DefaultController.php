<?php

namespace Venice\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is just a sandbox controller.
 *
 * Class DefaultController
 * @package Venice\AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     */
    public function indexAction(Request $request)
    {
        return $this->render(
            'VeniceAppBundle:default:index.html.twig',
            [
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            ]
        );
    }
}
