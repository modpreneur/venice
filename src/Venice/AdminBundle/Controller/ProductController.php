<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:17
 */

namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Entity\Product\FreeProduct;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenProduct;

class ProductController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     * @return Response
     * @throws \LogicException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     * @internal param Request $request
     *
     */
    public function indexAction()
    {
        $this->getBreadcrumbs()->addRouteItem('Products', 'admin_product_index');

        $entityManager = $this->getEntityManager();

        $max = $entityManager->getRepository('VeniceAppBundle:Product\Product')->count();
        $url = $this->generateUrl('grid_default', ['entity' => 'Product']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('name', 'Name');
        $gridConfBuilder->addColumn('defaultBillingPlan:initialPrice', 'Price', ['type' => 'double', 'allowOrder' => false]);
        $gridConfBuilder->addColumn('type', 'Type', ['allowOrder' => false]);
//        $gridConfBuilder->addColumn('updatedAt', 'Updated At', ['type' => 'date']);
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        return $this->render('VeniceAdminBundle:Product:index.html.twig', [
            'gridConfiguration' => $gridConfBuilder->getJSON(),
            'count' => $max
        ]);
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Product $product
     * @return string
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     * @internal param Request $request
     *
     */
    public function blogArticleIndexAction(Product $product)
    {
        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:BlogArticle')
            ->getCountByProduct($product->getId());
        $url = $this->generateUrl('grid_default', ['entity' => 'BlogArticle']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('title', 'Title');
        $gridConfBuilder->addColumn('handle', 'Handle');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);
        $gridConfBuilder->addColumn('products');

        $gridConfBuilder->setProperty('filter', 'products:id = ' . $product->getId());

        return $this->render(
            'VeniceAdminBundle:Product:articlesIndex.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Product $product
     * @return Response
     * @internal param Request $request
     */
    public function showAction(Product $product)
    {
        return $this->render(
            'VeniceAdminBundle:Product:show.html.twig',
            ['product' => $product]
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
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem($product->getName(), 'admin_product_tabs', ['id' => $product->getId()]);

        $necktieProductShowUrl = null;

        if ($product instanceof StandardProduct) {
            $necktieProductShowUrl = $this->getParameter('necktie_show_product_url');
            $necktieProductShowUrl = str_replace(':id', $product->getNecktieId(), $necktieProductShowUrl);
        }

        return $this->render(
            'VeniceAdminBundle:Product:tabs.html.twig',
            [
                'product' => $product,
                'necktieProductShowUrl' => $necktieProductShowUrl,
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function newAction()
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem('New product', 'admin_product_new');

        $product = $this->getEntityOverrideHandler()->getEntityInstance(FreeProduct::class);

        $form = $this->getFormCreator()
            ->createCreateForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['productType' => $product->getType(),]
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
     *
     * @param string $productType
     *
     * @return JsonResponse
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function createAction(Request $request, $productType)
    {
        try {
            $product = Product::createProductByType($productType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException('Product type: ' . $productType . ' not found.');
        }

        $em = $this->getEntityManager();

        $productForm = $this->getFormCreator()
            ->createCreateForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['productType' => $productType,]
            );

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($product);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage(),]
                    ]
                );
            }

            return new JsonResponse(
                [
                    'location' => $this->generateUrl('admin_product_tabs', ['id' => $product->getId()]),
                    'message' => $this->getParameter('flash_msg')['success_create']
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
     * @param Product $product
     * @return Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \LogicException
     * @internal param Request $request
     */
    public function editAction(Product $product)
    {
        $productForm = $this->getFormCreator()
            ->createEditForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['id' => $product->getId(),]
            );

        return $this->render(
            'VeniceAdminBundle:Product:edit.html.twig',
            [
                'entity' => $product,
                'form' => $productForm->createView(),
            ]

        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Product $product
     *
     * @return Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function deleteTabAction(Product $product)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product', $product->getId());

        return $this
            ->render(
                'VeniceAdminBundle:Product:tabDelete.html.twig',
                ['form' => $form->createView(),]
            );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function updateAction(Request $request, Product $product)
    {
        $productForm = $this->getFormCreator()
            ->createEditForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['id' => $product->getId()]
            );

        $em = $this->getEntityManager();

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($product);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ['error' => ['db' => $e->getMessage(),]],
                    400
                );
            }

            return new JsonResponse(
                ['message' => 'Product successfully updated',]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Product $product
     * @return JsonResponse
     * @throws \LogicException
     * @internal param Request $request
     */
    public function deleteAction(Product $product)
    {
        //remove all billing plans
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $entityManager->remove($product);
            $entityManager->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    'error' => ['db' => $e->getMessage()],
                    'message' => 'Could not delete.',
                ],
                400
            );
        }

        return new JsonResponse(
            [
                'message' => 'Product successfully deleted.',
                'location' => $this->generateUrl('admin_product_index'),
            ],
            302
        );
    }


    /**
     * @param Product $product
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function contentProductIndexAction(Product $product)
    {
        $url = $this->generateUrl('grid_default', ['entity' => 'ContentProduct']);
        $count = $this->getEntityManager()->getRepository('VeniceAppBundle:ContentProduct')
            ->getCountByProduct($product->getId());

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $count
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('content:name', 'Content');
        $gridConfBuilder->addColumn('orderNumber', 'Order number');
        $gridConfBuilder->addColumn('delay', 'Delay[hours]');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);
        $gridConfBuilder->addColumn('products');

        $gridConfBuilder->setProperty('filter', 'product:id = ' . $product->getId());

        return $this->render(
            'VeniceAdminBundle:Product:contentProductIndex.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $count,
                'product' => $product
            ]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param ContentProduct $contentProduct
     * @return Response
     * @internal param Request $request
     */
    public function contentProductShowAction(ContentProduct $contentProduct)
    {
        return $this->render(
            'VeniceAdminBundle:ContentProduct:show.html.twig',
            ['contentProduct' => $contentProduct]
        );
    }


    /**
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Product $product
     * @return Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @internal param Request $request
     */
    public function contentProductNewAction(Product $product)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem(
                $product->getName(),
                'admin_product_tabs',
                ['id' => $product->getId()]
            )
            ->addRouteItem(
                'New association',
                'admin_product_content_product_new',
                ['id' => $product->getId()]
            );

        $form = $this->getFormCreator()
            ->createCreateForm(
                $this->getEntityOverrideHandler()->getEntityInstance(ContentProduct::class),
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenProduct::class
                ),
                'admin_product_content_product',
                [],
                ['product' => $product,]
            );

        return $this->render(
            'VeniceAdminBundle:ContentProduct:new.html.twig',
            ['form' => $form->createView(),]
        );
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function contentProductEditAction(ContentProduct $contentProduct)
    {
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenProduct::class
                ),
                'admin_product_content_product',
                ['id' => $contentProduct->getId(),],
                ['product' => $contentProduct->getProduct()]
            );

        return $this->render(
            'VeniceAdminBundle:ContentProduct:edit.html.twig',
            [
                'entity' => $contentProduct,
                'form' => $contentForm->createView(),
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
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     *
     */
    public function contentProductUpdateAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenProduct::class
                ),
                'admin_product_content_product',
                ['id' => $contentProduct->getId(),],
                ['product' => $contentProduct->getProduct()]
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
                        'error' => ['db' => $e->getMessage(),],
                    ], 400
                );
            }

            return new JsonResponse(
                ['message' => 'Association successfully updated',]
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
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem(
                $contentProduct->getProduct()->getName(),
                'admin_product_tabs',
                ['id' => $contentProduct->getProduct()->getId()]
            )
            ->addRouteItem(
                'Association with ' . $contentProduct->getContent()->getName(),
                'admin_product_content_product_tabs',
                ['id' => $contentProduct->getId()]
            );

        return $this->render(
            'VeniceAdminBundle:Product:contentProductTabs.html.twig',
            ['contentProduct' => $contentProduct,]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function contentProductCreateAction(Request $request)
    {
        $contentProduct = $this->getEntityOverrideHandler()->getEntityInstance(ContentProduct::class);

        $form = $this->getFormCreator()
            ->createCreateForm(
                $contentProduct,
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenProduct::class
                ),
                'admin_product_content_product'
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
                        'error' => ['db' => $e->getMessage(),],
                    ],
                    400
                );
            }

            $url = $this->generateUrl(
                    'admin_product_tabs',
                    ['id' => $contentProduct->getProduct()->getId()]
                ) . '#tab3';

            return new JsonResponse(
                [
                    'message' => 'Successfully created',
                    'location' => $url
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
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function contentProductDeleteTabAction(ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product_content_product', $contentProduct->getId());

        return $this
            ->render(
                'VeniceAdminBundle:ContentProduct:tabDelete.html.twig',
                [
                    'form' => $form->createView()
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
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function contentProductDeleteAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product_content_product', $contentProduct->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getEntityManager();
                $em->remove($contentProduct);
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'error' => ['db' => $e->getMessage(),],
                        'message' => 'Could not delete.',
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                'message' => 'Association successfully deleted.',
                'location' => $this->generateUrl('admin_product_tabs', ['id' => $contentProduct->getProduct()->getId()]),
            ],
            302
        );
    }
}
