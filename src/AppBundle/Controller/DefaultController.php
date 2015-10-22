<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * This is just a sandbox controller.
 *
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     *
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $necktieGateway = $this->get("app.services.necktie_gateway");
        $user = $this->getUser();

        $necktieGateway->updateProductAccesses($user);
        $necktieGateway->getInvoices($user);

        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

}
