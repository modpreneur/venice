<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.01.16
 * Time: 12:08
 */

namespace Venice\AppBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PublicController
 */
class PublicController extends Controller
{
    /**
     * @return Response
     */
    public function publicAction()
    {
        return $this->render("VeniceAppBundle:public:welcome.html.twig");
    }
}