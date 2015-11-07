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

class AdminController extends Controller
{
    /**
     *
     * @Route("/admin", name="admin")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        return $this->render("TrinityAdminBundle::layout.html.twig");
    }
}