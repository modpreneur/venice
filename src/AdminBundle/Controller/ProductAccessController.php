<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace AdminBundle\Controller;

use AdminBundle\Form\ProductAccessType;
use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        $connectedToNecktie = $this->container->getParameter("necktie_url") !== null;

        return $this->render(
            ":AdminBundle/ProductAccess:index.html.twig",
            [
                "productAccesses" => $productAccesses,
                "user" => $user,
                "allowAddingNewProductAccesses" => !$connectedToNecktie,
                "displayNecktieField" => $connectedToNecktie,
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
        $connectedToNecktie = $this->container->getParameter("necktie_url") !== null;

        return $this->render(
            ":AdminBundle/ProductAccess:show.html.twig",
            [
                "productAccess" => $productAccess,
                "displayNecktieField" => $connectedToNecktie,
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

        $connectedToNecktie = $this->container->getParameter("necktie_url") !== null;

        return $this->render(
            ":AdminBundle/ProductAccess:tabs.html.twig",
            [
                "productAccess" => $productAccess,
                "displayEditTab" => !$connectedToNecktie,
                "displayDeleteTab" => !$connectedToNecktie
            ]
        );
    }


    /**
     * Display a form to edit a ProductAccess entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_product_access_edit")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")

     * @param ProductAccess $productAccess
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(ProductAccess $productAccess)
    {
        $productAccessForm = $this->get("admin.form_factory")
            ->createEditForm($this,
                $productAccess,
                new ProductAccessType($productAccess->getUser(), $this->getDoctrine()->getManager()),
                "admin_product_access",
                ["id" => $productAccess->getId(),]
            );

        return $this->render(
            ":AdminBundle/ProductAccess:edit.html.twig",
            [
                "entity" => $productAccess,
                "form" => $productAccessForm->createView(),
            ]

        );
    }


    /**
     * Process a request to update a ProductAccess entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_product_access_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")
     *
     * @param Request $request
     * @param ProductAccess $productAccess
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, ProductAccess $productAccess)
    {
        $em = $this->getEntityManager();

        $productAccessForm = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $productAccess,
                new ProductAccessType($productAccess->getUser(), $em),
                "admin_product",
                ["id" => $productAccess->getId()]
            );

        $productAccessForm->handleRequest($request);

        if ($productAccessForm->isValid()) {
            $em->persist($productAccess);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ["error" => ["db" => $e->getMessage(),]],
                    400
                );
            }

            return new JsonResponse(
                ["message" => "Product access successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productAccessForm);
        }
    }


    /**
     * Display a form to create a new ProductAccess entity.
     *
     * @Route("/new/{id}", name="admin_product_access_new")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     */
    public function newAction(User $user)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Users", "admin_user_index")
            ->addRouteItem($user->getFullNameOrUsername(), "admin_user_tabs", ["id" => $user->getId()])
            ->addRouteItem(
                "New product access",
                "admin_product_access_new",
                ["id" => $user->getId()]
            );

        $productAccess = new ProductAccess();

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $productAccess,
                new ProductAccessType($user, $this->getEntityManager()),
                "admin_product_access",
                ["id" => $user->getId(),]
            );

        return $this->render(
            ':AdminBundle/ProductAccess:new.html.twig',
            [
                'productAccess' => $productAccess,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to create a new ProductAccess entity.
     *
     * @Route("/create/{id}", name="admin_product_access_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, User $user)
    {
        $em = $this->getEntityManager();
        $productAccess = new ProductAccess();
        $type = new ProductAccessType($user, $this->getEntityManager());

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $productAccess,
                $type,
                "admin_product_access",
                ["id" => $user->getId(),]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($productAccess);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ['errors' => ['db' => $e->getMessage(),]]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Billing plan successfully created",
                    "location" => $this->generateUrl("admin_user_tabs", ["id" => $user->getId()])."#tab3",
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * @Route("/tab/{id}/delete", name="admin_product_access_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")
     *
     * @param ProductAccess $productAccess
     *
     * @return Response
     *
     */
    public function deleteTabAction(ProductAccess $productAccess)
    {
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_product_access", $productAccess->getId());

        return $this
            ->render(
                ":AdminBundle/ProductAccess:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * @Route("/{id}/delete", name="admin_product_access_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_ACCESS_EDIT')")
     *
     * @param ProductAccess $productAccess
     *
     * @return JsonResponse
     */
    public function deleteAction(ProductAccess $productAccess)
    {
        $user = $productAccess->getUser();
        try {
            $em = $this->getEntityManager();
            $em->remove($productAccess);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "error" => ["db" => $e->getMessage()],
                    "message" => "Could not delete.",
                ],
                400
            );
        }

        return new JsonResponse(
            [
                "message" => "ProductAccess successfully deleted.",
                "location" => $this->generateUrl("admin_user_tabs", ["id" => $user->getId()])."#tab3",
            ],
            302
        );
    }

}