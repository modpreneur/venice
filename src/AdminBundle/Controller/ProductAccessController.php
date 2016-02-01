<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace AdminBundle\Controller;

use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/product-access")
 *
 * Class ProductAccessController
 * @package AdminBundle\Controller
 */
class ProductAccessController extends BaseAdminController
{

    /**
     * Get all accesses of user
     *
     * @Route("/user/{id}", name="admin_product_access_user_index")
     * @Method("GET")
     * @View()
     *
     * @param User $user
     *
     * @return array
     */
    public function indexAction(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productAccesses = $entityManager->getRepository("AppBundle:ProductAccess")->findBy(["user" => $user]);

        return $this->render(
            "AdminBundle:ProductAccess:index.html.twig",
            [
                "productAccesses" => $productAccesses,
                "user" => $user,
            ]
        );
    }


    /**
     * @Route("/show/{id}", name="admin_product_access_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_VIEW')")
     *
     * @param ProductAccess $productAccess
     *
     * @return Response
     */
    public function showAction(ProductAccess $productAccess)
    {
        return $this->render(
            "AdminBundle:ProductAccess:show.html.twig",
            [
                "productAccess" => $productAccess,
            ]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_product_access_tabs")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_VIEW')")
     *
     * @param ProductAccess $productAccess
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(ProductAccess $productAccess)
    {
        $user = $productAccess->getUser();

        $this->getBreadcrumbs()
            ->addRouteItem("Users", "admin_user_index")
            ->addRouteItem($user->getFullNameOrUsername(), "admin_user_tabs", ["id" => $user->getId()])
            ->addRouteItem(
                "Product access ".$productAccess->getId(),
                "admin_product_access_tabs",
                ["id" => $productAccess->getId()]
            );

        return $this->render(
            "AdminBundle:ProductAccess:tabs.html.twig",
            [
                "productAccess" => $productAccess,
                "displayEditTab" => $this->getLogic()->displayEditTabForProductAccess(),
                "displayDeleteTab" => $this->getLogic()->displayDeleteTabForProductAccess()
            ]
        );
    }

}