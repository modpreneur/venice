<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.01.16
 * Time: 12:08
 */

namespace AppBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @Route("/public")
 *
 * Class PublicController
 */
class PublicController extends Controller
{
    /**
     *
     * @Route(path="/", name="public")
     *
     * @return Response
     */
    public function publicAction()
    {
        return $this->render("AppBundle:public:welcome.html.twig");
    }
}