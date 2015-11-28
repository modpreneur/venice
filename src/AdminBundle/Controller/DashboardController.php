<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.10.15
 * Time: 17:25
 */

namespace AdminBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     *
     * @Route("/admin", name="admin_dashboard")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(":AdminBundle:adminBase.html.twig");
    }
}