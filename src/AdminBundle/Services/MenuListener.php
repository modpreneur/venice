<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.10.15
 * Time: 17:17
 */

namespace AdminBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trinity\AdminBundle\Event\MenuEvent;

class MenuListener
{
    /** @var EntityManager */
    private $em;

    /** @var ContainerInterface */
    private $container;


    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param MenuEvent $event
     */
    public function onMenuConfigure(MenuEvent $event)
    {
        $menu = $event->getMenu('sidebar');

        $menu
            ->addChild('Dashboard', array('route' => 'admin_dashboard'))
            ->setAttribute('icon', 'trinity trinity-home')
            ->setExtra('orderNumber', 0);

        $menu
            ->addChild('Products', array('route' => 'admin_product_index'))
            ->setAttribute('icon', 'trinity trinity-products')
            ->setExtra('orderNumber', 1)
            ->setExtra('roles', ['ROLE_ADMIN_PRODUCT_VIEW']);

        $menu
            ->addChild('Contents', array('route' => 'admin_content_index'))
            ->setAttribute('icon', 'tiecons tiecons-video')
            ->setExtra('orderNumber', 2)
            ->setExtra('roles', ['ROLE_ADMIN_CONTENT_VIEW']);

        $menu
            ->addChild('Associations', array('route' => 'admin_content_product_index'))
            ->setAttribute('icon', 'tiecons tiecons-pin-right-negative')
            ->setExtra('orderNumber', 3)
            ->setExtra('roles', ['ROLE_ADMIN_CONTENT_PRODUCT_VIEW']);

        $menu
            ->addChild('Blog articles', array('route' => 'admin_blog_article_index'))
            ->setAttribute('icon', 'tiecons tiecons-book-text')
            ->setExtra('orderNumber', 4)
            ->setExtra('roles', ['ROLE_ADMIN_BLOG_VIEW']);

        $menu
            ->addChild('Users', array('route' => 'admin_user_index'))
            ->setAttribute('icon', 'tiecons tiecons-user-negative')
            ->setExtra('orderNumber', 5)
            ->setExtra('roles', ['ROLE_ADMIN_USER_VIEW']);
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
     * @throws \AppBundle\Exceptions\NavigationException
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