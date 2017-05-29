<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:17.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use ReflectionException;
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
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \LogicException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function indexAction(Request $request)
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
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('name', 'Name');
        $gridConfBuilder->addColumn(
            'defaultBillingPlan',
            'Price',
            ['type' => 'double', 'allowOrder' => false]
        );

        $gridConfBuilder->addColumn('productType', 'Type', ['allowOrder' => false]);
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        return $this->render('VeniceAdminBundle:Product:index.html.twig', [
            'gridConfiguration' => $gridConfBuilder->getJSON(),
            'count' => $max,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function blogArticleIndexAction(Request $request, Product $product)
    {
        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:BlogArticle')
            ->getCountByProduct($product->getId());
        $url = $this->generateUrl('grid_default', ['entity' => 'BlogArticle']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('title', 'Title');
        $gridConfBuilder->addColumn('handle', 'Handle');
        $gridConfBuilder->addColumn('products', 'Products');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        $gridConfBuilder->setProperty('filter', 'products:id = '.$product->getId());

        return $this->render(
            'VeniceAdminBundle:Product:articlesIndex.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max,
            ]
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
            'VeniceAdminBundle:Product:show.html.twig',
            ['product' => $product]
        );
    }

    /**
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
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem($product->getName(), 'admin_product_tabs', ['id' => $product->getId()]);

        $necktieProductShowUrl = null;

        if ($this->getEntityOverrideHandler()->isInstanceOf($product, StandardProduct::class)) {
            /** @var StandardProduct $necktieProductShowUrl */
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
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function newAction(Request $request)
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
                ['productType' => $product->getType()]
            );

        return $this->render(
            'VeniceAdminBundle:Product:new.html.twig',
            [
                'product' => $product,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param string  $productType
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function createAction(Request $request, string $productType)
    {
        try {
            $product = $this->getEntityOverrideHandler()->getEntityInstance(
                Product::createProductClassByType($productType)
            );
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException('Product type: '.$productType.' not found.');
        }

        /** @var $entityManager */
        $entityManager = $this->getEntityManager();

        $productForm = $this->getFormCreator()
            ->createCreateForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['productType' => $productType]
            );

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $entityManager->persist($product);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage()],
                    ]
                );
            }

            return new JsonResponse(
                [
                    'location' => $this->generateUrl('admin_product_tabs', ['id' => $product->getId()]),
                    'message' => $this->getParameter('flash_msg')['success_create'],
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
     * @return Response
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \LogicException
     */
    public function editAction(Request $request, Product $product)
    {
        $productForm = $this->getFormCreator()
            ->createEditForm(
                $product,
                $this->getEntityFormMatcher()->getFormClassForEntity($product),
                'admin_product',
                ['id' => $product->getId()]
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
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function deleteTabAction(Request $request, Product $product)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product', $product->getId());

        return $this
            ->render(
                'VeniceAdminBundle:Product:tabDelete.html.twig',
                ['form' => $form->createView()]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
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

        $entityManager = $this->getEntityManager();

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $entityManager->persist($product);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ['error' => ['db' => $e->getMessage()]],
                    400
                );
            }

            return new JsonResponse(
                ['message' => 'Product successfully updated']
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
     *
     * @throws \LogicException
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
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function contentProductIndexAction(Request $request, Product $product)
    {
        $url = $this->generateUrl('grid_default', ['entity' => 'ContentProduct']);
        $count = $this->getEntityManager()->getRepository('VeniceAppBundle:ContentProduct')
            ->getCountByProduct($product->getId());

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $count
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('content', 'Content');
        $gridConfBuilder->addColumn('orderNumber', 'Order number');
        $gridConfBuilder->addColumn('delay', 'Delay[hours]');
        $gridConfBuilder->addColumn('type', 'Type');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        $gridConfBuilder->setProperty('filter', 'product:id = '.$product->getId());

        return $this->render(
            'VeniceAdminBundle:Product:contentProductIndex.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $count,
                'product' => $product,
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_VIEW')")
     *
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductShowAction(Request $request, ContentProduct $contentProduct)
    {
        return $this->render(
            'VeniceAdminBundle:ContentProduct:show.html.twig',
            ['contentProduct' => $contentProduct]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return Response
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function contentProductNewAction(Request $request, Product $product)
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
                ['product' => $product]
            );

        return $this->render(
            'VeniceAdminBundle:ContentProduct:new.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function contentProductEditAction(Request $request, ContentProduct $contentProduct)
    {
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenProduct::class
                ),
                'admin_product_content_product',
                ['id' => $contentProduct->getId()],
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
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
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
                ['id' => $contentProduct->getId()],
                ['product' => $contentProduct->getProduct()]
            );

        $entityManager = $this->getEntityManager();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($contentProduct);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'error' => ['db' => $e->getMessage()],
                    ],
                    400
                );
            }

            return new JsonResponse(
                ['message' => 'Association successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_VIEW')")
     *
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductTabsAction(Request $request, ContentProduct $contentProduct)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem(
                $contentProduct->getProduct()->getName(),
                'admin_product_tabs',
                ['id' => $contentProduct->getProduct()->getId()]
            )
            ->addRouteItem(
                'Association with '.$contentProduct->getContent()->getName(),
                'admin_product_content_product_tabs',
                ['id' => $contentProduct->getId()]
            );

        return $this->render(
            'VeniceAdminBundle:Product:contentProductTabs.html.twig',
            ['contentProduct' => $contentProduct]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contentProduct);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'error' => ['db' => $e->getMessage()],
                    ],
                    400
                );
            }

            $url = $this->generateUrl(
                'admin_product_tabs',
                ['id' => $contentProduct->getProduct()->getId()]
            ).'#tab3';

            return new JsonResponse(
                [
                    'message' => 'Successfully created',
                    'location' => $url,
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
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function contentProductDeleteTabAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product_content_product', $contentProduct->getId());

        return $this
            ->render(
                'VeniceAdminBundle:ContentProduct:tabDelete.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request        $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function contentProductDeleteAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_product_content_product', $contentProduct->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $entityManager = $this->getEntityManager();
                $entityManager->remove($contentProduct);
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
        }

        return new JsonResponse(
            [
                'message' => 'Association successfully deleted.',
                'location' => $this->generateUrl(
                    'admin_product_tabs',
                    ['id' => $contentProduct->getProduct()->getId()]
                ),
            ],
            302
        );
    }
}
