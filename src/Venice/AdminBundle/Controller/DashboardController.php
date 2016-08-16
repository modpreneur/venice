<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.10.15
 * Time: 17:25.
 */
namespace Venice\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class DashboardController.
 */
class DashboardController extends BaseAdminController
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs();

        return $this->render('VeniceAdminBundle:Dashboard:dashboard.html.twig');
    }
}
