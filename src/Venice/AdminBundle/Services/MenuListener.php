<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.10.15
 * Time: 17:17.
 */
namespace Venice\AdminBundle\Services;

use Symfony\Component\Routing\RouterInterface;
use Trinity\AdminBundle\Event\MenuEvent;

class MenuListener
{
    /** @var  RouterInterface */
    protected $router;

    /**
     * MenuListener constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param MenuEvent $event
     *
     * @throws \InvalidArgumentException
     * @throws \Trinity\AdminBundle\Exception\MenuException
     */
    public function onMenuConfigure(MenuEvent $event)
    {
        $menu = $event->getMenu('sidebar');

        $menu
            ->addChild('Dashboard', ['route' => 'admin_dashboard'])
            ->setAttribute('icon', 'trinity trinity-home')
            ->setExtra('orderNumber', 10);

        $menu
            ->addChild('Products', ['route' => 'admin_product_index'])
            ->setAttribute('icon', 'trinity trinity-products')
            ->setExtra('orderNumber', 20)
            ->setExtra('roles', ['ROLE_ADMIN_PRODUCT_VIEW']);

        $menu
            ->addChild('Content', ['route' => 'admin_content_index'])
            ->setAttribute('icon', 'tiecons tiecons-video')
            ->setExtra('orderNumber', 30)
            ->setExtra('roles', ['ROLE_ADMIN_CONTENT_VIEW']);

        $blogUri = $this->router->generate('admin_blog_tabs');
        $blogMenuItem = $menu->addChild('Blog', ['uri' => '#'])
            ->setAttribute('icon', 'trinity trinity-projects2')
            ->setAttribute('dropdown', true)
            ->setAttribute('custom-attributes', ['data-ng-scope' => 'blog-menu'])
            ->setExtra('orderNumber', 60)
            ->setExtra('roles', ['ROLE_ADMIN_BLOG_VIEW'])
        ;
        $blogMenuItem->addChild('Articles', ['uri' => $blogUri . '#tab1']);
        $blogMenuItem->addChild('Categories', ['uri' => $blogUri . '#tab2']);
        $blogMenuItem->addChild('Tags', ['uri' => $blogUri . '#tab3']);

        $menu
            ->addChild('Users', ['route' => 'admin_user_index'])
            ->setAttribute('icon', 'trinity trinity-users')
            ->setExtra('orderNumber', 50)
            ->setExtra('roles', ['ROLE_ADMIN_USER_VIEW']);

        $loggerMenuItem = $menu->addChild('Loggers', ['uri' => '#'])
            ->setAttribute('icon', 'trinity trinity-calendar')
            ->setAttribute('dropdown', true)
            ->setAttribute('custom-attributes', ['data-ng-scope' => 'logger-menu'])
            ->setExtra('orderNumber', 60)
            ->setExtra('roles', ['ROLE_ADMIN_LOGGER_VIEW'])
        ;

        $lUri = $this->router->generate('admin_logger_index');
        $loggerMenuItem->addChild('Exception log', ['uri' => $lUri . '#tab1']);
        $loggerMenuItem->addChild('Ipn log', ['uri' => $lUri . '#tab3']);
        $loggerMenuItem->addChild('Entity action log', ['uri' => $lUri . '#tab4']);
        $loggerMenuItem->addChild('Access log', ['uri' => $lUri . '#tab5']);
        $loggerMenuItem->addChild('Payment error log', ['uri' => $lUri . '#tab6']);
        $loggerMenuItem->addChild('Ban log', ['uri' => $lUri . '#tab7']);
        $loggerMenuItem->addChild('Message log', ['uri' => $lUri . '#tab8']);

        //
        //$menu
        //    ->addChild('Newsletters', array('route' => 'newsletter'))
        //    ->setAttribute('icon', 'tiecons tiecons-share')
        //    ->setExtra('orderNumber', 4)
        //    ->setExtra('roles', ['ROLE_ADMIN_NEWSLETTER_VIEW']);
        //
        //$menu
        //    ->addChild('Settings', array('route' => 'settings_tabs'))
        //    ->setAttribute('icon', 'tiecons tiecons-settings-two-wheels')
        //    ->setExtra('orderNumber', 5)
        //    ->setExtra('roles', ['ROLE_ADMIN_SETTING_GLOBAL', 'ROLE_ADMIN_SETTING_MAIL', 'ROLE_ADMIN_SETTING_PAYSYSTEM', 'ROLE_ADMIN_SETTING_GROUPS']);
        //
        //$menu
        //    ->addChild('Tools')
        //    ->setAttribute('icon', 'tiecons tiecons-clock')
        //    ->setAttribute('dropdown', true)
        //    ->setExtra('orderNumber', 6)
        //    ->setExtra('roles', ['ROLE_ADMIN_JOBS', 'ROLE_ADMIN_LOGGER']);
        //
        //$menu['Tools']->addChild('Jobs', array('route' => 'jobs'))
        //              ->setAttribute('icon', 'tiecons tiecons-menu-bold')
        //              ->setExtra('orderNumber', 0)
        //              ->setExtra('roles', ['ROLE_ADMIN_JOBS']);
        //
        //$menu['Tools']->addChild('Logger', array('route' => 'logger'))
        //              ->setAttribute('icon', 'tiecons tiecons-clock')
        //              ->setExtra('orderNumber', 1)
        //              ->setExtra('roles', ['ROLE_ADMIN_LOGGER']);
    }

    /**
     * @param MenuEvent $event
     */
    public function onUserMenuConfigure(MenuEvent $event)
    {
        //$menu = $event->getMenu('user_menu');
        //
        //$menu
        //    ->addChild('Show Profile', array('route' => 'fos_user_profile_show'))
        //    ->setExtra('orderNumber', 0);
        //
        //$menu
        //    ->addChild('Edit Profile', array('route' => 'fos_user_profile_edit'))
        //    ->setExtra('orderNumber', 1);
        //
        //$menu
        //    ->addChild('Change Password', array('route' => 'fos_user_change_password'))
        //    ->setExtra('orderNumber', 2);
        //
        //$menu
        //    ->addChild('Logout', array('route' => 'fos_user_security_logout'))
        //    ->setExtra('orderNumber', 3);
    }

    /**
     * @param MenuEvent $event
     *
     * @throws \Venice\AppBundle\Exceptions\NavigationException
     */
    public function onQuickMenuConfigure(MenuEvent $event)
    {
        //$menu = $event->getMenu('quick-menu');
        //
        //$menu
        //    ->addChild('Add user', array('route' => 'user_new'))
        //    ->setAttribute('icon', 'tiecons tiecons-user-negative')
        //    ->setAttribute('type', 'test');
        //
        //$menu
        //    ->addChild('Add client', array('route' => 'client_new'))
        //    ->setAttribute('icon', 'tiecons tiecons-bookmark-3');
        //
        //$menu
        //    ->addChild('Add product', array('route' => 'product_new'))
        //    ->setAttribute('icon', 'tiecons tiecons-page-list');
    }
}
