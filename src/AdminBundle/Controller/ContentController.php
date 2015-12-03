<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 07.11.15
 * Time: 13:43
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Content\Content;
use AppBundle\Entity\Content\GroupContent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/content")
 *
 * Class ContentController
 * @package AdminBundle\Controller
 */
class ContentController extends BaseAdminController
{

    /**
     * @Route("", name="admin_content_index")
     * @Route("/")
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $contents = $this->getEntityManager()->getRepository("AppBundle:Content\\Content")->findAll();

        return $this->render(
            ":AdminBundle/Content:index.html.twig",
            [
                "contents" => $contents
            ]
        );
    }


    /**
     * Display a form to create a new Content entity.
     *
     * @Route("/new/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_new")
     * @Method("GET")
     *
     * @param Request $request
     * @param         $contentType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $contentType)
    {
        try
        {
            $content = Content::createContentByType($contentType);
        }
        catch(ReflectionException $e)
        {
            throw new NotFoundHttpException("Content type: " . $contentType . " not found.");
        }

        $form = $this->createForm(
            $content->getFormType(),
            $content,
            [
                'action' => $this->generateUrl(
                    'admin_content_create',
                    [
                        "contentType" => $contentType
                    ]
                ),
            ]
        );

        return $this->render(
            ':AdminBundle/Content:new.html.twig',
            [
                'entity'     => $content,
                'form'       => $form->createView()
            ]
        );
    }


    /**
     * Process a request to create a new Content entity.
     *
     * @Route("/create/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @param         $contentType
     *
     * @return JsonResponse
     *
     */
    public function createAction(Request $request, $contentType)
    {
        try
        {
            $content = Content::createContentByType($contentType);
        }
        catch(ReflectionException $e)
        {
            throw new NotFoundHttpException("Content type: " . $contentType . " not found.");
        }

        $em = $this->getEntityManager();

        $contentForm = $this->createForm($content->getFormType(), $content);
        $contentForm->handleRequest($request);

        if($contentForm->isValid())
        {
            $em->persist($content);
            $em->flush();

            return new JsonResponse(["message" => "Content successfully created"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($contentForm);
        }
    }


    /**
     * Display a form to edit a Content entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_content_edit")
     * @Method("GET")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Content $content)
    {
        $contentType = $content->getFormType();
        $contentForm = $this->createForm(
            $contentType,
            $content,
            [
                "action" => $this->generateUrl(
                    "admin_content_update",
                    [
                        "id" => $content->getId()
                    ]
                )
            ]
        );

        return $this->render(
            ($content instanceof GroupContent)?
                ":AdminBundle/Content/Group:edit.html.twig"
                : ":AdminBundle/Content/:edit.html.twig"
            ,
            [
                "entity" => $content,
                "form" => $contentForm->createView()
            ]
        );
    }


    /**
     * Process a request to update a Content entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_content_update")
     * @Method("POST")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, Content $content)
    {
        if($content instanceof GroupContent)
        {
            return $this->updateGroupContentAction($request, $content);
        }
        else
        {
            return $this->updateNonGroupContentAction($request, $content);
        }
    }


    /**
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     */
    protected function updateNonGroupContentAction(Request $request, Content $content)
    {
        $contentType = $content->getFormType();
        $contentForm = $this->createForm($contentType, $content);
        $em = $this->getEntityManager();

        $contentForm->handleRequest($request);

        if($contentForm->isValid())
        {
            $em->persist($content);

            try
            {
                $em->flush();
            }
            catch (DBALException $e)
            {
                return new JsonResponse(["errors" => ["db" => $e->getMessage()]]);
            }

            return new JsonResponse(["message" => "Content successfully updated"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($contentForm);
        }
    }

    protected function updateGroupContentAction(Request $request, GroupContent $content)
    {
        $contentType = $content->getFormType();
        $contentForm = $this->createForm($contentType, $content);
        $em = $this->getEntityManager();

        //Copy original items
        $originalItems = new ArrayCollection();
        foreach ($content->getItems() as $item)
        {
            $originalItems->add($item);
        }

        $contentForm->handleRequest($request);

        if($contentForm->isValid())
        {
            foreach ($originalItems as $originalItem)
            {
                // If the item is not in the field data it was removed. So remove it from collection.
                if(!$content->getItems()->contains($originalItem))
                {
                    $em->remove($originalItem);
                }
            }

            $em->persist($content);

            try
            {
                $em->flush();
            }
            catch (DBALException $e)
            {
                return new JsonResponse(["errors" => ["db" => $e->getMessage()]]);
            }

            return new JsonResponse(["message" => "Content successfully updated"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($contentForm);
        }
    }


    /**
     * @Route("/{id}/delete", name="admin_content_delete")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Content $content)
    {
        try
        {
            $em = $this->getEntityManager();
            $em->remove($content);
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

        return new JsonResponse(["message" => "Content successfully deleted."]);
    }
}