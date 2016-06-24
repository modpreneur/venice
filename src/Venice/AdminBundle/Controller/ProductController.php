<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:17
 */

namespace Venice\AdminBundle\Controller;

use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Entity\Product\FreeProduct;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Event\AppEvents;
use Venice\AppBundle\Event\FreeProductCreatedEvent;
use Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenProduct;
use Venice\AppBundle\Form\Product\StandardProductType;
use Venice\AppBundle\Services\GridConfigurationBuilder;
use Doctrine\DBAL\DBALException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()->addRouteItem("Products", "admin_product_index");

        $products = $this->getEntityManager()->getRepository("VeniceAppBundle:Product\\Product")->findAll();

        $entityManager = $this->getEntityManager();

        $max = $entityManager->getRepository('VeniceAppBundle:Product\Product')->count();
        $url = $this->generateUrl('grid_default', ['entity'=>'Product']);

        $builder = new GridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $builder->addColumn('id', 'Id');
        $builder->addColumn('name', 'Name');

        return $this->render('VeniceAdminBundle:Product:index.html.twig', ['gridConfiguration'=>$builder->getJSON(), "products" => $products]);
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     * 
     * @param Request $request
     *
     * @return string
     */
    public function blogArticleIndexAction(Request $request, Product $product)
    {
        return $this->render(
            "VeniceAdminBundle:Product:articlesIndex.html.twig",
            ["blogArticles" => $product->getBlogArticles(),]
        );
    }



    /**
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
            "VeniceAdminBundle:Product:show.html.twig",
            ["product" => $product]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(Product $product)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem($product->getName(), "admin_product_tabs", ["id" => $product->getId()]);

        $necktieProductShowUrl = null;

        if ($product instanceof StandardProduct) {
            $necktieProductShowUrl = $this->getParameter("necktie_show_product_url");
            $necktieProductShowUrl = str_replace(":id", $product->getNecktieId(), $necktieProductShowUrl);
        }

        return $this->render(
            "VeniceAdminBundle:Product:tabs.html.twig",
            [
                "product" => $product,
                "necktieProductShowUrl" => $necktieProductShowUrl,
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem("New product", "admin_product_new");

        //Standard is defined in template, too
        $product = new StandardProduct();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $product,
                StandardProductType::class,
                "admin_product",
                ["productType" => $product->getType(),]
            );

        return $this->render(
            'VeniceAdminBundle:Product:new.html.twig',
            [
                'product' => $product,
                'form' => $form->createView()
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param         $productType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function newFormAction(Request $request, $productType)
    {
        try {
            $product = Product::createProductByType($productType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Product type: ".$productType." not found.");
        }
        $form = $this->getFormCreator()
            ->createCreateForm(
                $product,
                $product->getFormTypeClass(),
                "admin_product",
                ["productType" => $productType,]
            );

        return $this->render(
            'VeniceAdminBundle:Product:newForm.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
                'productType' => $productType,
            ]
        );
    }


    /**
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

        $productForm = $this->getFormCreator()
            ->createCreateForm(
                $product,
                $product->getFormTypeClass(),
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
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     * 
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {
        $productForm = $this->getFormCreator()
            ->createEditForm(
                $product,
                $product->getFormTypeClass(),
                "admin_product",
                ["id" => $product->getId(),]
            );

        return $this->render(
            "VeniceAdminBundle:Product:edit.html.twig",
            [
                "entity" => $product,
                "form" => $productForm->createView(),
            ]

        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Product $product
     *
     * @return Response
     */
    public function deleteTabAction(Product $product)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_product", $product->getId());

        return $this
            ->render(
                "VeniceAdminBundle:Product:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, Product $product)
    {
        $productForm = $this->getFormCreator()
            ->createEditForm(
                $product,
                $product->getFormTypeClass(),
                "admin_product",
                ["id" => $product->getId()]
            );

        $em = $this->getEntityManager();

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($product);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ["error" => ["db" => $e->getMessage(),]],
                    400
                );
            }

            return new JsonResponse(
                ["message" => "Product successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Product $product)
    {
        //remove all billing plans
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $entityManager->remove($product);
            $entityManager->flush();
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
                "message" => "Product successfully deleted.",
                "location" => $this->generateUrl("admin_product_index"),
            ],
            302
        );
    }


    /**
     * @param Product $product
     *
     * @return Response
     */
    public function contentProductIndexAction(Product $product)
    {
        return $this->render(
            "VeniceAdminBundle:Product:contentProductIndex.html.twig",
            [
                "contentProducts" => $product->getContentProducts(),
                "product" => $product
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductShowAction(Request $request, ContentProduct $contentProduct)
    {
        return $this->render(
            "VeniceAdminBundle:ContentProduct:show.html.twig",
            ["contentProduct" => $contentProduct]
        );
    }


    /**
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     */
    public function contentProductNewAction(Request $request, Product $product)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem(
                $product->getName(),
                "admin_product_tabs",
                ["id" => $product->getId()]
            )
            ->addRouteItem(
                "New association",
                "admin_product_content_product_new",
                ["id" => $product->getId()]
            );

        $form = $this->getFormCreator()
            ->createCreateForm(
                new ContentProduct(),
                ContentProductTypeWithHiddenProduct::class,
                "admin_product_content_product",
                [],
                ["product" => $product,]
            );

        return $this->render("VeniceAdminBundle:ContentProduct:new.html.twig",
            ["form" => $form->createView(),]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contentProductEditAction(ContentProduct $contentProduct)
    {
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                ContentProductTypeWithHiddenProduct::class,
                "admin_product_content_product",
                ["id" => $contentProduct->getId(),],
                ["product" => $contentProduct->getProduct()]
            );

        return $this->render(
            "VeniceAdminBundle:ContentProduct:edit.html.twig",
            [
                "entity" => $contentProduct,
                "form" => $contentForm->createView(),
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     *
     */
    public function contentProductUpdateAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                ContentProductTypeWithHiddenProduct::class,
                "admin_product_content_product",
                ["id" => $contentProduct->getId(),],
                ["product" => $contentProduct->getProduct()]
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
                        "error" => ["db" => $e->getMessage(),],
                    ], 400
                );
            }

            return new JsonResponse(
                ["message" => "Association successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_VIEW')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductTabsAction(ContentProduct $contentProduct)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem(
                $contentProduct->getProduct()->getName(),
                "admin_product_tabs",
                ["id" => $contentProduct->getProduct()->getId()]
            )
            ->addRouteItem(
                "Association with ".$contentProduct->getContent()->getName(),
                "admin_product_content_product_tabs",
                ["id" => $contentProduct->getId()]
            );

        return $this->render(
            "VeniceAdminBundle:Product:contentProductTabs.html.twig",
            ["contentProduct" => $contentProduct,]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contentProductCreateAction(Request $request)
    {
        $contentProduct = new ContentProduct();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $contentProduct,
                ContentProductTypeWithHiddenProduct::class,
                "admin_product_content_product"
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
                        "error" => ["db" => $e->getMessage(),],
                    ],
                    400
                );
            }

            $url = $this->generateUrl(
                    "admin_product_tabs",
                    ["id" => $contentProduct->getProduct()->getId()]
                )."#tab3";

            return new JsonResponse(
                [
                    "message" => "Successfully created",
                    "location" => $url
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }

    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductDeleteTabAction(ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_product_content_product", $contentProduct->getId());

        return $this
            ->render(
                "VeniceAdminBundle:ContentProduct:tabDelete.html.twig",
                [
                    "form" => $form->createView()
                ]
            );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     */
    public function contentProductDeleteAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_product_content_product", $contentProduct->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getEntityManager();
                $em->remove($contentProduct);
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),],
                        "message" => "Could not delete.",
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                "message" => "Association successfully deleted.",
                "location" => $this->generateUrl("admin_product_tabs", ["id" => $contentProduct->getProduct()->getId()]),
            ],
            302
        );
    }
}