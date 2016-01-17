<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 13:27
 */

namespace FrontBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/front")
 *
 * Class FrontController
 */
class FrontController extends Controller
{
    /**
     * @Route("/", name="front_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $groups = $this->getDoctrine()->getRepository("AppBundle:Content\\GroupContent")->findAll();

        return $this->render(
            ":FrontBundle/Front:index.html.twig",
            ["groups" => $groups,]
        );
    }
}