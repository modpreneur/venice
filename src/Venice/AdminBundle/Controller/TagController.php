<?php

namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\Tag;

/**
 * Class TagController.
 */
class TagController extends BaseAdminController
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
        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:Tag')->count();
        $url = $this->generateUrl('grid_default', ['entity' => 'Tag']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('name', 'Name');
        $gridConfBuilder->addColumn('handle', 'Handle');
        $gridConfBuilder->addColumn('blogArticles', 'Articles');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        return $this->render(
            'VeniceAdminBundle:Tag:index.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max,
            ]
        );
    }

    /**
     * Render page for tag tabs.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request  $request
     * @param Tag $tag
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function tabsAction(Request $request, Tag $tag)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Blog', 'admin_blog_tabs')
            ->addItem('Tags', $this->generateUrl('admin_blog_tabs').'#tab3')
            ->addRouteItem($tag->getName(), 'admin_tag_tabs', ['id' => $tag->getId()]);

        return $this->render(
            'VeniceAdminBundle:Tag:tabs.html.twig',
            ['tag' => $tag]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request  $request
     * @param Tag $tag
     *
     * @return Response
     */
    public function showAction(Request $request, Tag $tag)
    {
        return $this->render(
            'VeniceAdminBundle:Tag:show.html.twig',
            ['tag' => $tag]
        );
    }

    /**
     * Display a form to create a new Tag entity.
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
            ->addItem('Tags', $this->generateUrl('admin_blog_tabs').'#tab3')
            ->addRouteItem('New tag', 'admin_tag_new');

        $tag = $this->getEntityOverrideHandler()->getEntityInstance(Tag::class);
        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $tag,
                $this->getEntityFormMatcher()->getFormClassForEntity($tag),
                'admin_tag'
            );

        return $this->render(
            'VeniceAdminBundle:Tag:new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Process a request to create a new Tag entity.
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
        $tag = $this->getEntityOverrideHandler()->getEntityInstance(Tag::class);

        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $tag,
                $this->getEntityFormMatcher()->getFormClassForEntity($tag),
                'admin_tag'
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager->persist($tag);
            $entityManager->flush();

            return new JsonResponse(
                [
                    'message' => 'Tag successfully created',
                    'location' => $this->generateUrl(
                        'admin_tag_tabs',
                        ['id' => $tag->getId()]
                    ),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * Display a form to edit a Tag entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request  $request
     * @param Tag $tag
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
    public function editAction(Tag $tag)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $tag,
                $this->getEntityFormMatcher()->getFormClassForEntity($tag),
                'admin_tag',
                ['id']
            );

        return $this->render(
            'VeniceAdminBundle:Tag:edit.html.twig',
            [
                'entity' => $tag,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Process a request to update a Tag entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request  $request
     * @param Tag $tag
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
    public function updateAction(Request $request, Tag $tag)
    {
        $tagForm = $this->getFormCreator()
            ->createEditForm(
                $tag,
                $this->getEntityFormMatcher()->getFormClassForEntity($tag),
                'admin_tag'
            );

        $entityManager = $this->getEntityManager();

        $tagForm->handleRequest($request);

        if ($tagForm->isValid()) {
            $entityManager->persist($tag);

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
                ['message' => 'Blog tag successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($tagForm);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request  $request
     * @param Tag $tag
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
    public function deleteTabAction(Request $request, Tag $tag)
    {
        $formFactory = $this->getFormCreator();
        $form = $formFactory->createDeleteForm(
            'admin_tag',
            $tag->getId()
        );

        return $this
            ->render(
                'VeniceAdminBundle:Tag:tabDelete.html.twig',
                ['form' => $form->createView()]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request  $request
     * @param Tag $tag
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
    public function deleteAction(Request $request, Tag $tag)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_tag', $tag->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $entityManager = $this->getEntityManager();
                $entityManager->remove($tag);
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
                'message' => 'Blog tag successfully deleted.',
                'location' => $this->generateUrl('admin_blog_tabs'),
            ],
            302
        );
    }
}
