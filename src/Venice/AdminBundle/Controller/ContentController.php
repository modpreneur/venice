<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 07.11.15
 * Time: 13:43.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Content\ContentInGroup;
use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Entity\Content\VideoContent;
use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenContent;

/**
 * Class ContentController.
 */
class ContentController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Contents', 'admin_content_index');

        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:Content\Content')->count();
        $url = $this->generateUrl('grid_default', ['entity' => 'Content']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('name', 'Name');
        $gridConfBuilder->addColumn('type', 'Type');
        $gridConfBuilder->addColumn('contentProducts', 'Products');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        return $this->render(
            'VeniceAdminBundle:Content:index.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     *
     * @throws \LogicException
     */
    public function indexForContentAction(Request $request, Content $content)
    {
        $contentProducts = $this
            ->getEntityManager()
            ->getRepository('VeniceAppBundle:ContentProduct')
            ->findBy(['content' => $content]);

        return $this->render(
            'VeniceAdminBundle:ContentProduct:contentIndex.html.twig',
            ['contentProducts' => $contentProducts]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function showAction(Request $request, Content $content)
    {
        return $this->render(
            'VeniceAdminBundle:Content:show'.ucfirst($content->getType()).'.html.twig',
            ['content' => $content]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function tabsAction(Request $request, Content $content)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Contents', 'admin_content_index')
            ->addRouteItem($content->getName(), 'admin_content_tabs', ['id' => $content->getId()]);

        return $this->render('VeniceAdminBundle:Content:tabs.html.twig', ['content' => $content]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \LogicException
     */
    public function newAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Contents', 'admin_content_index')
            ->addRouteItem('New content', 'admin_content_new');

        $formOptions = [];

        $content = $this->getEntityOverrideHandler()->getEntityInstance(VideoContent::class);

        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content',
                ['contentType' => $content->getType()],
                $formOptions
            );

        return $this->render(
            'VeniceAdminBundle:Content:new.html.twig',
            [
                'entity' => $content,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param $contentType
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    public function newFormAction(Request $request, string $contentType)
    {
        try {
            $contentClass = Content::createContentClassByType($contentType);
            $content = $this->getEntityOverrideHandler()->getEntityInstance($contentClass);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException('Content type: '.$contentType.' not found.');
        }
        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content',
                ['contentType' => $contentType]
            );

        // Remove items field for now. todo: remove it in future?
        // Explanation:
        // The ContentInGroupType requires group id to fill it in the hidden field.
        // But when the group is creating, there is no id.
        // This could be solved by saving the group first, getting it's id and then saving it's content.
        $form->remove('items');

        return $this->render(
            'VeniceAdminBundle:Content:newForm.html.twig',
            [
                'content' => $content,
                'form' => $form->createView(),
                'contentType' => $contentType,
            ]
        );
    }

    /**
     * Process a request to create a new Content entity.
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param string  $contentType
     *
     * @return JsonResponse
     *
     * @throws \InvalidArgumentException
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
    public function createAction(Request $request, string $contentType)
    {
        try {
            $contentClass = Content::createContentClassByType($contentType);
            $content = $this->getEntityOverrideHandler()->getEntityInstance($contentClass);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException('Content type: '.$contentType.' not found.');
        }

        $entityManager = $this->getEntityManager();

        $formOptions = [];

        if ($this->getEntityOverrideHandler()->isInstanceOf($content, GroupContent::class)) {
            $formOptions = [
                'groupContent' => ($this->getEntityOverrideHandler()->isInstanceOf($content, GroupContent::class)) ?
                    $content : null,
            ];
        }

        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content',
                ['contentType' => $contentType],
                $formOptions
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($content);
            $entityManager->flush();

            return new JsonResponse(
                [
                    'message' => 'Content successfully created',
                    'location' => $this->generateUrl(
                        'admin_content_tabs',
                        ['id' => $content->getId()]
                    ),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * Display a form to edit a Content entity.
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \LogicException
     */
    public function editAction(Request $request, Content $content)
    {
        $formOptions = [
            'groupContent' => ($this->getEntityOverrideHandler()->isInstanceOf($content, GroupContent::class)) ?
                $content : null
        ];

        $form = $this->getFormCreator()
            ->createEditForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content',
                ['groupContent' => $content],
                $formOptions
            );

        return $this->render(
            'VeniceAdminBundle:Content:edit.html.twig',
            [
                'entity' => $content,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Process a request to update a Content entity.
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function updateAction(Request $request, Content $content)
    {
        if ($this->getEntityOverrideHandler()->isInstanceOf($content, GroupContent::class)) {
            return $this->updateGroupContent($request, $content);
        } else {
            return $this->updateNonGroupContent($request, $content);
        }
    }

    /**
     * @param Request $request
     * @param Content $content
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
    protected function updateNonGroupContent(Request $request, Content $content)
    {
        $entityManager = $this->getEntityManager();

        $form = $this->getFormCreator()
            ->createEditForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content'
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($content);

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
                ['message' => 'Content successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * @param Request      $request
     * @param GroupContent $content
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
    protected function updateGroupContent(Request $request, GroupContent $content)
    {
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $content,
                $this->getEntityFormMatcher()->getFormClassForEntity($content),
                'admin_content',
                ['groupContent' => $content]
            );

        $entityManager = $this->getEntityManager();

        //Copy original items
        $originalItems = new ArrayCollection();
        foreach ($content->getItems() as $item) {
            $originalItems->add($item);
        }

        $contentForm->handleRequest($request);

        if ($contentForm->isValid()) {
            foreach ($originalItems as $originalItem) {
                // If the item is not in the field data it was removed. So remove it from collection.
                if (!$content->getItems()->contains($originalItem)) {
                    $entityManager->remove($originalItem);
                }
            }

            $entityManager->persist($content);

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
                ['message' => 'Content successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($contentForm);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
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
    public function deleteTabAction(Request $request, Content $content)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_content', $content->getId());

        return $this
            ->render(
                'VeniceAdminBundle:Content:tabDelete.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     * @throws \InvalidArgumentException
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
    public function deleteAction(Request $request, Content $content)
    {
        $entityManager = $this->getEntityManager();

        $form = $this->getFormCreator()
            ->createDeleteForm('admin_content', $content->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                // First, remove all associations of the group
                if ($this->getEntityOverrideHandler()->isInstanceOf($content, GroupContent::class)) {
                    /** @var ContentInGroup $item */
                    foreach ($content->getItems() as $item) {
                        $entityManager->remove($item);
                    }
                }

                $entityManager->remove($content);
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
                'message' => 'Content successfully deleted.',
                'location' => $this->generateUrl('admin_content_index'),
            ],
            302
        );
    }

    /**
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function contentProductIndexAction(Request $request, Content $content)
    {
        $url = $this->generateUrl('grid_default', ['entity' => 'ContentProduct']);
        $count = $this->getEntityManager()->getRepository('VeniceAppBundle:ContentProduct')
            ->getCountByContent($content->getId());

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $count
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('product:name', 'Product');
        $gridConfBuilder->addColumn('orderNumber', 'Order number');
        $gridConfBuilder->addColumn('delay', 'Delay[hours]');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        $gridConfBuilder->setProperty('filter', 'content='.$content->getId());

        return $this->render(
            'VeniceAdminBundle:Content:contentProductIndex.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $count,
                'content' => $content,
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function contentProductNewAction(Request $request, Content $content)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Products', 'admin_content_index')
            ->addRouteItem(
                $content->getName(),
                'admin_content_tabs',
                ['id' => $content->getId()]
            )
            ->addRouteItem(
                'New association',
                'admin_content_content_product_new',
                ['id' => $content->getId()]
            );

        $form = $this->getFormCreator()
            ->createCreateForm(
                $this->getEntityOverrideHandler()->getEntityInstance(ContentProduct::class),
                $this->getFormOverrideHandler()->getFormClass(
                    ContentProductTypeWithHiddenContent::class
                ),
                'admin_content_content_product',
                [],
                ['content' => $content]
            );

        return $this->render(
            'VeniceAdminBundle:ContentProduct:new.html.twig',
            ['form' => $form->createView()]
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
                    ContentProductTypeWithHiddenContent::class
                ),
                'admin_content_content_product'
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
                'admin_content_tabs',
                ['id' => $contentProduct->getContent()->getId()]
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
                    ContentProductTypeWithHiddenContent::class
                ),
                'admin_content_content_product',
                ['id' => $contentProduct->getId()],
                ['content' => $contentProduct->getContent()]
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
                    ContentProductTypeWithHiddenContent::class
                ),
                'admin_content_content_product',
                ['id' => $contentProduct->getId()],
                ['content' => $contentProduct->getContent()]
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
            ->addRouteItem('Contents', 'admin_content_index')
            ->addRouteItem(
                $contentProduct->getContent()->getName(),
                'admin_content_tabs',
                ['id' => $contentProduct->getContent()->getId()]
            )
            ->addRouteItem(
                'Association with '.$contentProduct->getProduct()->getName(),
                'admin_content_content_product_tabs',
                ['id' => $contentProduct->getId()]
            );

        return $this->render(
            'VeniceAdminBundle:Content:contentProductTabs.html.twig',
            ['contentProduct' => $contentProduct]
        );
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
            ->createDeleteForm('admin_content_content_product', $contentProduct->getId());

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
            ->createDeleteForm('admin_content_content_product', $contentProduct->getId());

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
                    'admin_content_tabs',
                    ['id' => $contentProduct->getContent()->getId()]
                ),
            ],
            302
        );
    }
}
