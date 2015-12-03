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
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/blogArticle")
 *
 * Class BlogArticleController
 * @package AdminBundle\Controller
 */
class BlogArticleController extends BaseAdminController
{
    /**
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
            [
                "blogArticles" => $blogArticles
            ]
        );
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
        $blogArticle = new BlogArticle();
        $form = $this->createForm(
            new BlogArticleType(),
            $blogArticle,
            [
                'action' => $this->generateUrl(
                    'admin_blog_article_create'
                ),
            ]
        );

        return $this->render(
            ':AdminBundle/BlogArticle:new.html.twig',
            [
                'entity'     => $blogArticle,
                'form'       => $form->createView()
            ]
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

        $blogArticleForm = $this->createForm(new BlogArticleType(), $blogArticle);
        $blogArticleForm->handleRequest($request);

        if($blogArticleForm->isValid())
        {
            $em->persist($blogArticle);
            $em->flush();

            return new JsonResponse(["message" => "BlogArticle successfully created"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($blogArticleForm);
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
        $blogArticleForm = $this->createForm(
            new BlogArticleType(),
            $blogArticle,
            [
                "action" => $this->generateUrl(
                    "admin_blog_article_update",
                    [
                        "id" => $blogArticle->getId()
                    ]
                )
            ]
        );

        return $this->render(
            "AdminBundle/BlogArticle/edit.html.twig",
            [
                "entity" => $blogArticle,
                "form" => $blogArticleForm->createView()
            ]
        );
    }


    /**
     * Process a request to update a BlogArticle entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_blog_article_update")
     * @Method("POST")
     *
     * @param Request $request
     * @param BlogArticle $blogArticle
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, BlogArticle $blogArticle)
    {
        $blogArticleForm = $this->createForm(new BlogArticleType(), $blogArticle);
        $em = $this->getEntityManager();

        $blogArticleForm->handleRequest($request);

        if($blogArticleForm->isValid())
        {
            $em->persist($blogArticle);

            try
            {
                $em->flush();
            }
            catch (DBALException $e)
            {
                return new JsonResponse(["errors" => ["db" => $e->getMessage()]]);
            }

            return new JsonResponse(["message" => "BlogArticle successfully updated"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($blogArticleForm);
        }
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
        try
        {
            $em = $this->getEntityManager();
            $em->remove($blogArticle);
            $em->flush();
        }
        catch(DBALException $e)
        {
            return new JsonResponse(
                [
                    "errors" => ["db" => $e->getMessage()],
                    "message" => "Could not delete."
                ]
            );
        }

        return new JsonResponse(["message" => "BlogArticle successfully deleted."]);
    }
}