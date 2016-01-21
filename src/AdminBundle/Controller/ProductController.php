<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:17
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Product\FreeProduct;
use AppBundle\Entity\Product\Product;
use AppBundle\Event\AppEvents;
use AppBundle\Event\FreeProductCreatedEvent;
use Doctrine\DBAL\DBALException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/product")
 */
class ProductController extends BaseAdminController
{
    /**
     * @Route("", name="admin_product_index")
     * @Route("/")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()->addRouteItem("Products", "admin_product_index");

        $entityManager = $this->getEntityManager();
        $products = $entityManager->getRepository("AppBundle:Product\\Product")->findAll();

        return $this->render(
            ":AdminBundle/Product:index.html.twig",
            ["products" => $products,]
        );
    }


    /**
     * @Route("/show/{id}", name="admin_product_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     */
    public function showAction(Request $request, Product $product)
    {
        return $this->render(
            ":AdminBundle/Product:show.html.twig",
            ["product" => $product]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_product_tabs")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(Request $request, Product $product)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem($product->getName(), "admin_product_tabs", ["id" => $product->getId()]);

        return $this->render(
            ":AdminBundle/Product:tabs.html.twig",
            ["product" => $product,]
        );
    }


    /**
     * Display a form to create a new Product entity.
     *
     * @Route("/new/{productType}",requirements={"productType": "\w+"}, name="admin_product_new")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param         $productType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function newAction(Request $request, $productType)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem("New product", "admin_product_new", ["productType" => $productType]);

        try {
            $product = Product::createProductByType($productType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Product type: ".$productType." not found.");
        }

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $product,
                $product->getFormType(),
                "admin_product",
                ["productType" => $productType,]
            );

        return $this->render(
            ':AdminBundle/Product:new.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
                'productType' => $productType,
            ]
        );
    }


    /**
     * Process a request to create a new Product entity.
     *
     * @Route("/create/{productType}",requirements={"productType": "\w+"}, name="admin_product_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     *
     * @param string $productType
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, $productType)
    {
        try {
            $product = Product::createProductByType($productType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Product type: ".$productType." not found.");
        }

        $em = $this->getEntityManager();

        $productForm = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $product,
                $product->getFormType(),
                "admin_product",
                ["productType" => $productType,]
            );

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($product);

            try {
                $em->flush();

                if ($product instanceof FreeProduct) {
                    $this->get("event_dispatcher")->dispatch(AppEvents::FREE_PRODUCT_CREATED, new FreeProductCreatedEvent($product));
                }
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage(),]
                    ]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Product successfully created",
                    "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()]),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * Display a form to edit a Product entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_product_edit")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {
        $productForm = $this->get("admin.form_factory")
            ->createEditForm($this,
                $product,
                $product->getFormType(),
                "admin_product",
                ["id" => $product->getId(),]
            );

        return $this->render(
            ":AdminBundle/Product:edit.html.twig",
            [
                "entity" => $product,
                "form" => $productForm->createView(),
            ]

        );
    }


    /**
     * @Route("/tab/{id}/delete", name="admin_product_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Product $product
     *
     * @return Response
     *
     */
    public function deleteTabAction(Product $product)
    {
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_product", $product->getId());

        return $this
            ->render(
                ":AdminBundle/Product:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * Process a request to update a Product entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_product_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, Product $product)
    {
        $productForm = $this->get("admin.form_factory")
            ->createEditForm($this, $product, $product->getFormType(), "admin_product", ["id" => $product->getId()]);

        $em = $this->getEntityManager();

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($product);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),]
                    ],
                    400
                );
            }

            return new JsonResponse(
                [
                    "message" => "Product successfully updated",
                ]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * @Route("/{id}/delete", name="admin_product_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Product $product)
    {
        try {
            $em = $this->getEntityManager();
            $em->remove($product);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "errors" => ["db" => $e->getMessage()],
                    "message" => "Could not delete.",
                ],
                400
            );
        }

        return new JsonResponse(
            [
                "message" => "Product successfully deleted.",
                "location" => $this->generateUrl("admin_product_index"),
            ],
            302
        );
    }
}