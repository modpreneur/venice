<?php

namespace Venice\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class BlogController
 */
class BlogController extends BaseAdminController
{
    public function tabsAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Blog', 'admin_blog_tabs');


        return $this->render('@VeniceAdmin/Blog/tabs.html.twig');
    }
}