<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 15:03
 */

namespace AdminBundle\Controller;

use AdminBundle\Form\Content\ContentProductType;
use AppBundle\Entity\ContentProduct;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/admin/contentProduct")
 *
 * Class ContentProductController
 * @package AdminBundle\Controller
 */
class ContentProductController extends BaseAdminController
{

    /**
     * @Route("", name="admin_content_product_index")
     * @Route("/")
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $contentProducts = $this
            ->getEntityManager()
            ->getRepository("AppBundle:ContentProduct")
            ->findAll();

        return $this->render(
            ":AdminBundle/ContentProduct:index.html.twig",
            ["contentProducts" => $contentProducts,]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_content_product_tabs")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(Request $request, ContentProduct $contentProduct)
    {
        return $this->render(
            ":AdminBundle/ContentProduct:tabs.html.twig",
            ["contentProduct" => $contentProduct,]
        );
    }


    /**
     * Display a form to create a new ContentProduct entity.
     *
     * @Route("/new", name="admin_content_product_new")
     * @Method("GET")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $form = $this->get("admin.form_factory")
            ->createCreateForm($this,
                new ContentProduct(),
                new ContentProductType(),
                "admin_content_product"
            );

        return $this->render(":AdminBundle/ContentProduct:new.html.twig",
            ["form" => $form->createView(),]
        );
    }


    /**
     * Process a request to create a new ContentProduct entity.
     *
     * @Method("POST")
     * @Route("/create", name="admin_content_product_create")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $contentProduct = new ContentProduct();
        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $contentProduct,
                new ContentProductType(),
                "admin_content_product"
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contentProduct);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => [
                            "db" => $e->getMessage(),],
                    ]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Association successfully created",
                    "location" => $this->generateUrl("admin_content_tabs")."#tab2"
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * Display a form to edit a ContentProduct entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_content_product_edit")
     * @Method("GET")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, ContentProduct $contentProduct)
    {
        $contentForm = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $contentProduct,
                new ContentProductType(),
                "admin_content_product",
                ["id" => $contentProduct->getId(),]
            );

        return $this->render(
            ":AdminBundle/ContentProduct:edit.html.twig",
            [
                "entity" => $contentProduct,
                "form" => $contentForm->createView(),
            ]
        );
    }


    /**
     * Process a request to update a ContentProduct entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_content_product_update")
     * @Method("PUT")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     *
     */
    public function updateAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $contentProduct,
                new ContentProductType(),
                "admin_content_product",
                ["id" => $contentProduct->getId(),]
            );

        $em = $this->getEntityManager();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($contentProduct);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => ["db" => $e->getMessage(),],
                    ]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Association successfully updated",
                    "location" => $this->generateUrl("admin_content_product_tabs", ["id" => $contentProduct->getId(),])
                ],
                302);
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * @Route("/tab/{id}/delete", name="admin_content_product_delete_tab")
     *
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function deleteTabAction(ContentProduct $contentProduct)
    {
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_content_product", $contentProduct->getId());

        return $this
            ->render(
                ":AdminBundle/ContentProduct:tabDelete.html.twig",
                [
                    "form" => $form->createView()
                ]
            );
    }


    /**
     * @Route("/{id}/delete", name="admin_content_product_delete")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, ContentProduct $contentProduct)
    {
        try {
            $em = $this->getEntityManager();
            $em->remove($contentProduct);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "errors" => ["db" => $e->getMessage(),],
                    "message" => "Could not delete.",
                ]
            );
        }

        return new JsonResponse(
            [
                "message" => "Association successfully deleted.",
                "location" => $this->generateUrl("admin_content_tabs")."#tab2",
            ],
            302);
    }
}