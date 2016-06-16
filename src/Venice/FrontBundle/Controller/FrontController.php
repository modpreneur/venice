<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 13:27
 */

namespace Venice\FrontBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FrontController
 */
class FrontController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $groups = $this->getDoctrine()->getRepository("VeniceAppBundle:Content\\GroupContent")->findAll();

        return $this->render(
            "VeniceFrontBundle:Front:index.html.twig",
            ["groups" => $groups,]
        );
    }
}