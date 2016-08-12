<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.10.15
 * Time: 17:25
 */

namespace Venice\AdminBundle\Controller;

/**
 * Class DashboardController
 * @package Venice\AdminBundle\Controller
 */
class DashboardController extends BaseAdminController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     *
     */
    public function indexAction()
    {
        $this->getBreadcrumbs();

        return $this->render('VeniceAdminBundle:Dashboard:dashboard.html.twig');
    }
}
