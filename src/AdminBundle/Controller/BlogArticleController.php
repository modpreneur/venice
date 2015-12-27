<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:05
 */

namespace AdminBundle\Controller;


use AdminBundle\Form\BlogArticleType;
use AppBundle\Entity\BlogArticle;
use AppBundle\Entity\Product\Product;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/blogArticle")
 *
 * Class BlogArticleController
 * @package AdminBundle\Controller
 */
class BlogArticleController extends BaseAdminController
{
    /**
     * Display list of blog articles.
     *
     * @Route("", name="admin_blog_article_index")
     * @Route("/")
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $blogArticles = $this->getEntityManager()->getRepository("AppBundle:BlogArticle")->findAll();

        return $this->render(
            ":AdminBundle/BlogArticle:index.html.twig",
            ["blogArticles" => $blogArticles,]
        );
    }


    /**
     * Render page for blog article tabs.
     *
     * @Route("/tabs/{id}", name="admin_blog_article_tabs")
     *
     * @param BlogArticle $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(BlogArticle $article)
    {
        if (!$article) {
            throw $this->createNotFoundException('Unable to find BlogArticle entity.');
        }

        return $this->render(
            ':AdminBundle/BlogArticle:tabs.html.twig',
            ['article' => $article,]);
    }


    /**
     * Display a form to create a new BlogArticle entity.
     *
     * @Route("/new", name="admin_blog_article_new")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $form = $this
            ->get("admin.form_factory")
            ->createCreateForm(
                $this,
                new BlogArticle(),
                new BlogArticleType(),
                "admin_blog_article"
            );

        return $this->render(
            ':AdminBundle/BlogArticle:new.html.twig',
            ['form' => $form->createView(),]
        );
    }


    /**
     * Process a request to create a new BlogArticle entity.
     *
     * @Route("/create", name="admin_blog_article_create")
     * @Method("POST")
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
            ->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $blogArticle,
                new BlogArticleType(),
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
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_blog_article_edit")
     * @Method("GET")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BlogArticle $blogArticle)
    {
        $form = $this->get("admin.form_factory")
            ->createEditForm($this,
                $blogArticle,
                new BlogArticleType(),
                'admin_blog_article', ["id",]
            );

        return $this->render(
            "AdminBundle/BlogArticle/edit.html.twig",
            [
                "entity" => $blogArticle,
                "form" => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to update a BlogArticle entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_blog_article_update")
     * @Method("PUT")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, BlogArticle $blogArticle)
    {
        $blogArticleForm = $this->get("admin.form_factory")
            ->createEditForm($this,
                $blogArticle,
                new BlogArticleType(),
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
     * @Route("/tab/blogArticle/{id}/delete", name="admin_blog_article_delete_tab")
     *
     * @param BlogArticle $blogArticle
     *
     * @return Response
     */
    public function deleteTabAction(BlogArticle $blogArticle)
    {
        $formFactory = $this->get("admin.form_factory");
        $form = $formFactory->createDeleteForm(
            $this,
            "admin_blog_article",
            $blogArticle->getId()
        );

        return $this
            ->render(
                ":AdminBundle/BlogArticle:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }

    /**
     * @Route("/{id}/delete", name="admin_blog_article_delete")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, BlogArticle $blogArticle)
    {
        try {
            $em = $this->getEntityManager();
            $em->remove($blogArticle);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "errors" => ["db" => $e->getMessage(),],
                    "message" => "Could not delete."
                ]
            );
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