<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace Venice\AdminBundle\Controller;

use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductAccessController
 * @package Venice\AdminBundle\Controller
 */
class ProductAccessController extends BaseAdminController
{

    /**
     * Get all accesses of user
     *
     * @param User $user
     *
     * @return array
     */
    public function indexAction(User $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $productAccesses = $entityManager->getRepository("VeniceAppBundle:ProductAccess")->findBy(["user" => $user]);

        $necktieUrl = $this->getParameter("necktie_show_product_access_url");

        return $this->render(
            "VeniceAdminBundle:ProductAccess:index.html.twig",
            [
                "productAccesses" => $productAccesses,
                "user" => $user,
                "necktieUrl" => $necktieUrl
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_VIEW')")
     *
     * @param ProductAccess $productAccess
     *
     * @return Response
     */
    public function showAction(ProductAccess $productAccess)
    {
        return $this->render(
            "VeniceAdminBundle:ProductAccess:show.html.twig",
            [
                "productAccess" => $productAccess,
            ]
        );
    }


    /**
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

        $necktieUrl = $this->getParameter("necktie_show_product_access_url");
        $necktieUrl = str_replace(":userId", $user->getNecktieId(), $necktieUrl);
        $necktieUrl = str_replace(":productAccessId", $productAccess->getNecktieId(), $necktieUrl);

        return $this->render(
            "VeniceAdminBundle:ProductAccess:tabs.html.twig",
            [
                "productAccess" => $productAccess,
                "displayEditTab" => $this->getLogic()->displayEditTabForProductAccess(),
                "displayDeleteTab" => $this->getLogic()->displayDeleteTabForProductAccess(),
                "necktieProductAccessShowUrl" => $necktieUrl
            ]
        );
    }

}