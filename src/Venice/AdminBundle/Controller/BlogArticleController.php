<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:05
 */

namespace Venice\AdminBundle\Controller;


use Venice\AppBundle\Entity\BlogArticle;
use Venice\AppBundle\Form\BlogArticleType;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BlogArticleController
 * @package Venice\AdminBundle\Controller
 */
class BlogArticleController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Blog articles", "admin_blog_article_index");

        $max = $this->getEntityManager()->getRepository('VeniceAppBundle:BlogArticle')->count();
        $url = $this->generateUrl('grid_default', ['entity'=>'BlogArticle']);

        $gridConfBuilder =  $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $max,
            15
        // TODO @JakubFajkus  add limit_on_page to venice (trinity settings)
        );

        // Defining columns
        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('title', 'Title');
        $gridConfBuilder->addColumn('handle', 'Handle');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        

        return $this->render(
            "VeniceAdminBundle:BlogArticle:index.html.twig",
            [
                'gridConfiguration'=>$gridConfBuilder->getJSON(),
                'count' => $max
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
     */
    public function tabsAction(BlogArticle $article)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Blog articles", "admin_blog_article_index")
            ->addRouteItem($article->getTitle(), "admin_blog_article_tabs", ["id" => $article->getId()]);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        return $this->render(
            'VeniceAdminBundle:BlogArticle:tabs.html.twig',
            ['article' => $article,]);
    }


    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_VIEW')")
     *
     * @param Request $request
     * @param BlogArticle $article
     *
     * @return Response
     */
    public function showAction(Request $request, BlogArticle $article)
    {
        return $this->render(
            "VeniceAdminBundle:BlogArticle:show.html.twig",
            ["article" => $article]
        );
    }


    /**
     * Display a form to create a new BlogArticle entity.
     *
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Blog articles", "admin_blog_article_index")
            ->addRouteItem("New blog article", "admin_blog_article_new");

        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                new BlogArticle(),
                BlogArticleType::class,
                "admin_blog_article"
            );
//        $dateFormat = $this->get('trinity.settings')->get('date');
        $dateFormat = 'y-m-d';
        return $this->render(
            'VeniceAdminBundle:BlogArticle:new.html.twig',
            [
                'form' => $form->createView(),
                'dateFormat' => $dateFormat,
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
     */
    public function createAction(Request $request)
    {
        $em = $this->getEntityManager();
        $blogArticle = new BlogArticle();

        $form = $this
            ->getFormCreator()
            ->createCreateForm(
                $blogArticle,
                BlogArticleType::class,
                "admin_blog_article"
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($blogArticle);
            $em->flush();

            return new JsonResponse(
                [
                    "message" => "Blog article successfully created",
                    "location" => $this->generateUrl(
                        "admin_blog_article_tabs",
                        ["id" => $blogArticle->getId(),]
                    )
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BlogArticle $blogArticle)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $blogArticle,
                BlogArticleType::class,
                'admin_blog_article', ["id",]
            );
        //        $dateFormat = $this->get('trinity.settings')->get('date');
        $dateFormat = 'y-m-d';
        return $this->render(
            "VeniceAdminBundle:BlogArticle:edit.html.twig",
            [
                "entity" => $blogArticle,
                "form" => $form->createView(),
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
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, BlogArticle $blogArticle)
    {
        $blogArticleForm = $this->getFormCreator()
            ->createEditForm(
                $blogArticle,
                BlogArticleType::class,
                "admin_blog_article"
            );

        $em = $this->getEntityManager();

        $blogArticleForm->handleRequest($request);

        if ($blogArticleForm->isValid()) {
            $em->persist($blogArticle);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => ["db" => $e->getMessage(),],
                        "message" => "Could not update"
                    ]
                );
            }

            return new JsonResponse(
                ["message" => "Blog article successfully updated",]
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
     */
    public function deleteTabAction(BlogArticle $blogArticle)
    {
        $formFactory = $this->getFormCreator();
        $form = $formFactory->createDeleteForm(
            "admin_blog_article",
            $blogArticle->getId()
        );

        return $this
            ->render(
                "VeniceAdminBundle:BlogArticle:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BLOG_EDIT')")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, BlogArticle $blogArticle)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_blog_article", $blogArticle->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getEntityManager();
                $em->remove($blogArticle);
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => ["db" => $e->getMessage(),],
                        "message" => "Could not delete."
                    ],
                    400
                );
            }
        }


        return new JsonResponse(
            [
                "message" => "Blog article successfully deleted.",
                "location" => $this->generateUrl("admin_blog_article_index"),
            ],
            302
        );
    }
}