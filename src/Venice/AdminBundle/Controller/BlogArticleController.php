<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:05.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\BlogArticle;
use Venice\AppBundle\Entity\Category;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\Tag;

/**
 * Class BlogArticleController.
 */
class BlogArticleController extends BaseAdminController
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
        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:BlogArticle')->count();
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
        $gridConfBuilder->addColumn('categories', 'Categories');
        $gridConfBuilder->addColumn('tags', 'Tags');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false, 'className' => 'cell-center']);

        return $this->render(
            'VeniceAdminBundle:BlogArticle:index.html.twig',
            [
                'gridConfiguration' => $gridConfBuilder->getJSON(),
                'count' => $max,
            ]
        );
    }

    /**
     * Render page for blog article tabs.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param BlogArticle $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function tabsAction(Request $request, BlogArticle $article)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Blog', 'admin_blog_tabs')
            ->addItem('Articles', $this->generateUrl('admin_blog_tabs').'#tab1')
            ->addRouteItem($article->getTitle(), 'admin_blog_article_tabs', ['id' => $article->getId()]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        return $this->render(
            'VeniceAdminBundle:BlogArticle:tabs.html.twig',
            ['article' => $article]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param BlogArticle $article
     *
     * @return Response
     */
    public function showAction(Request $request, BlogArticle $article)
    {
        return $this->render(
            'VeniceAdminBundle:BlogArticle:show.html.twig',
            ['article' => $article]
        );
    }

    /**
     * Display a form to create a new BlogArticle entity.
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
            ->addItem('Articles', $this->generateUrl('admin_blog_tabs').'#tab1')
            ->addRouteItem('New blog article', 'admin_blog_article_new');

        $blogArticle = $this->getEntityOverrideHandler()->getEntityInstance(BlogArticle::class);
        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $blogArticle,
                $this->getEntityFormMatcher()->getFormClassForEntity($blogArticle),
                'admin_blog_article'
            );

        $dateFormat = $this->get('trinity.settings')->get('date_format');
        $productCount = $this->getDoctrine()->getRepository(Product::class)->count();
        $categoryCount = $this->getDoctrine()->getRepository(Category::class)->count();
        $tagCount = $this->getDoctrine()->getRepository(Tag::class)->count();

        return $this->render(
            'VeniceAdminBundle:BlogArticle:new.html.twig',
            [
                'form' => $form->createView(),
                'dateFormat' => $dateFormat,
                'productCount' => $productCount,
                'categoryCount' => $categoryCount,
                'tagCount' => $tagCount,
            ]
        );
    }

    /**
     * Process a request to create a new BlogArticle entity.
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
        $blogArticle = $this->getEntityOverrideHandler()->getEntityInstance(BlogArticle::class);

        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $blogArticle,
                $this->getEntityFormMatcher()->getFormClassForEntity($blogArticle),
                'admin_blog_article'
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $blogArticle->setPublisher($this->getUser());

            $entityManager->persist($blogArticle);
            $entityManager->flush();

            return new JsonResponse(
                [
                    'message' => 'Blog article successfully created',
                    'location' => $this->generateUrl(
                        'admin_blog_article_tabs',
                        ['id' => $blogArticle->getId()]
                    ),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * Display a form to edit a BlogArticle entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
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
    public function editAction(Request $request, BlogArticle $blogArticle)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $blogArticle,
                $this->getEntityFormMatcher()->getFormClassForEntity($blogArticle),
                'admin_blog_article',
                ['id']
            );
        $dateFormat = $this->get('trinity.settings')->get('date_format');

        return $this->render(
            'VeniceAdminBundle:BlogArticle:edit.html.twig',
            [
                'entity' => $blogArticle,
                'form' => $form->createView(),
                'dateFormat' => $dateFormat,
                'dateVal' => $blogArticle->getDateToPublish()->getTimestamp(),
            ]
        );
    }

    /**
     * Process a request to update a BlogArticle entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request     $request
     * @param BlogArticle $blogArticle
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
    public function updateAction(Request $request, BlogArticle $blogArticle)
    {
        $blogArticleForm = $this->getFormCreator()
            ->createEditForm(
                $blogArticle,
                $this->getEntityFormMatcher()->getFormClassForEntity($blogArticle),
                'admin_blog_article'
            );

        $entityManager = $this->getEntityManager();

        $blogArticleForm->handleRequest($request);

        if ($blogArticleForm->isValid()) {
            $entityManager->persist($blogArticle);

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
                ['message' => 'Blog article successfully updated']
            );
        } else {
            return $this->returnFormErrorsJsonResponse($blogArticleForm);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param BlogArticle $blogArticle
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
    public function deleteTabAction(Request $request, BlogArticle $blogArticle)
    {
        $formFactory = $this->getFormCreator();
        $form = $formFactory->createDeleteForm(
            'admin_blog_article',
            $blogArticle->getId()
        );

        return $this
            ->render(
                'VeniceAdminBundle:BlogArticle:tabDelete.html.twig',
                ['form' => $form->createView()]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request     $request
     * @param BlogArticle $blogArticle
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
    public function deleteAction(Request $request, BlogArticle $blogArticle)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_blog_article', $blogArticle->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $entityManager = $this->getEntityManager();
                $entityManager->remove($blogArticle);
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
                'message' => 'Blog article successfully deleted.',
                'location' => $this->generateUrl('admin_blog_tabs'),
            ],
            302
        );
    }
}
