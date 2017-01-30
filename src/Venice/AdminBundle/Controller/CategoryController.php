<?php

namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\Category;

/**
 * Class CategoryController
 */
class CategoryController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     *
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \LogicException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function indexAction(Request $request)
    {
        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:Category')->count();
        $url = $this->generateUrl('grid_default', ['entity' => 'Category']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('name', 'Name');
        $gridConfBuilder->addColumn('handle', 'Handle');
        $gridConfBuilder->addColumn('articles', 'Articles');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        return $this->render(
            'VeniceAdminBundle:Category:index.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max,
            ]
        );
    }

    /**
     * Render page for category tabs.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     * @param Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function tabsAction(Request $request, Category $category)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Blog', 'admin_blog_tabs')
            ->addItem('Categories', $this->generateUrl('admin_blog_tabs').'#tab2')
            ->addRouteItem($category->getName(), 'admin_category_tabs', ['id' => $category->getId()]);

        return $this->render(
            'VeniceAdminBundle:Category:tabs.html.twig',
            ['category' => $category]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     */
    public function showAction(Request $request, Category $category)
    {
        return $this->render(
            'VeniceAdminBundle:Category:show.html.twig',
            ['category' => $category]
        );
    }

    /**
     * Display a form to create a new Category entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function newAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Blog', 'admin_blog_tabs')
            ->addItem('Categories', $this->generateUrl('admin_blog_tabs').'#tab2')
            ->addRouteItem('New category', 'admin_category_new');

        $category = $this->getEntityOverrideHandler()->getEntityInstance(Category::class);
        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $category,
                $this->getEntityFormMatcher()->getFormClassForEntity($category),
                'admin_category'
            );

        return $this->render(
            'VeniceAdminBundle:Category:new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Process a request to create a new Category entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function createAction(Request $request)
    {
        $entityManager = $this->getEntityManager();
        $category = $this->getEntityOverrideHandler()->getEntityInstance(Category::class);

        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $category,
                $this->getEntityFormMatcher()->getFormClassForEntity($category),
                'admin_category'
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return new JsonResponse(
                [
                    'message' => 'Category successfully created',
                    'location' => $this->generateUrl(
                        'admin_category_tabs',
                        ['id' => $category->getId()]
                    ),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * Display a form to edit a Category entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     *
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function editAction(Category $category)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $category,
                $this->getEntityFormMatcher()->getFormClassForEntity($category),
                'admin_category',
                ['id']
            );

        return $this->render(
            'VeniceAdminBundle:Category:edit.html.twig',
            [
                'entity' => $category,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Process a request to update a Category entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request     $request
     * @param Category $category
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function updateAction(Request $request, Category $category)
    {
        $categoryForm = $this->getFormCreator()
            ->createEditForm(
                $category,
                $this->getEntityFormMatcher()->getFormClassForEntity($category),
                'admin_category'
            );

        $entityManager = $this->getEntityManager();

        $categoryForm->handleRequest($request);

        if ($categoryForm->isValid()) {
            $entityManager->persist($category);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage()],
                        'message' => 'Could not update',
                    ]
                );
            }

            return new JsonResponse(
                ['message' => 'Blog category successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($categoryForm);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     * @param Category $category
     *
     * @return Response
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function deleteTabAction(Request $request, Category $category)
    {
        $formFactory = $this->getFormCreator();
        $form = $formFactory->createDeleteForm(
            'admin_category',
            $category->getId()
        );

        return $this
            ->render(
                'VeniceAdminBundle:Category:tabDelete.html.twig',
                ['form' => $form->createView()]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request     $request
     * @param Category $category
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_category', $category->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $entityManager = $this->getEntityManager();
                $entityManager->remove($category);
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage()],
                        'message' => 'Could not delete.',
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                'message' => 'Blog category successfully deleted.',
                'location' => $this->generateUrl('admin_blog_tabs'),
            ],
            302
        );
    }
}