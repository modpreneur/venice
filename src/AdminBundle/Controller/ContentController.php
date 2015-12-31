<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 07.11.15
 * Time: 13:43
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Content\Content;
use AppBundle\Entity\Content\ContentInGroup;
use AppBundle\Entity\Content\GroupContent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $contents = $this->getEntityManager()->getRepository("AppBundle:Content\\Content")->findAll();

        return $this->render(
            ":AdminBundle/Content:index.html.twig",
            ["contents" => $contents,]
        );
    }


    /**
     * @Route("/show/{id}", name="admin_content_show")
     * @Method("GET")
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
            ":AdminBundle/Content:show".ucfirst($content->getType()).".html.twig",
            ["content" => $content]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_content_tabs")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function tabsAction(Request $request, Content $content)
    {
        return $this->render(":AdminBundle/Content:tabs.html.twig", ["content" => $content,]);
    }


    /**
     * Display a form to create a new Content entity.
     *
     * @Route("/new/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_new")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param         $contentType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $contentType)
    {
        try {
            $content = Content::createContentByType($contentType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Content type: ".$contentType." not found.");
        }

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $content,
                $content->getFormType(
                    [
                        $content,
                        $this->getEntityManager(),
                    ]
                ),
                "admin_content",
                ["contentType" => $contentType,]
            );
        // Remove items field for now. todo: remove it in future?
        // Explanation:
        // The ContentInGroupType requires group id to fill it in the hidden field. But when the group is creating, there is no id.
        // This could be solved by saving the group first, getting it's id and then saving it's content.
        $form->remove("items");

        return $this->render(
            ':AdminBundle/Content:new.html.twig',
            [
                'entity' => $content,
                'form' => $form->createView()
            ]
        );
    }


    /**
     * Process a request to create a new Content entity.
     *
     * @Route("/create/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param         $contentType
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, $contentType)
    {
        try {
            $content = Content::createContentByType($contentType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Content type: ".$contentType." not found.");
        }

        $em = $this->getEntityManager();

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $content,
                $content->getFormType(
                    [
                        $content,
                        $this->getEntityManager()
                    ]
                ),
                "admin_content",
                ["contentType" => $contentType]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($content);
            $em->flush();

            return new JsonResponse(
                [
                    "message" => "Content successfully created",
                    "location" => $this->generateUrl(
                        "admin_content_tabs",
                        ["id" => $content->getId(),]
                    )
                ]
                ,
                302
            );

        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * Display a form to edit a Content entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_content_edit")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Content $content)
    {
        $form = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $content,
                $content->getFormType(
                    [
                        $content,
                        $this->getEntityManager(),
                    ]
                ),
                "admin_content"
            );

        return $this->render(
            ":AdminBundle/Content:edit.html.twig",
            [
                "entity" => $content,
                "form" => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to update a Content entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_content_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, Content $content)
    {
        if ($content instanceof GroupContent) {
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
     */
    protected function updateNonGroupContent(Request $request, Content $content)
    {
        $em = $this->getEntityManager();

        $form = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $content,
                $content->getFormType(),
                "admin_content"
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($content);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => ["db" => $e->getMessage(),]
                    ]
                );
            }

            return new JsonResponse(
                ["message" => "Content successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * @param Request $request
     * @param GroupContent $content
     *
     * @return JsonResponse
     */
    protected function updateGroupContent(Request $request, GroupContent $content)
    {
        $contentForm = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $content,
                $content->getFormType(
                    [
                        $content,
                        $this->getEntityManager()
                    ]
                ),
                "admin_content"
            );

        $em = $this->getEntityManager();

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
                    $em->remove($originalItem);
                }
            }

            $em->persist($content);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "errors" => ["db" => $e->getMessage(),]
                    ]
                );
            }

            return new JsonResponse(
                ["message" => "Content successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($contentForm);
        }
    }


    /**
     * @Route("/tab/{id}/delete", name="admin_content_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Content $content
     *
     * @return Response
     *
     */
    public function deleteTabAction(Content $content)
    {
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_content", $content->getId());

        return $this
            ->render(
                ":AdminBundle/Content:tabDelete.html.twig",
                [
                    "form" => $form->createView()
                ]
            );
    }


    /**
     * @Route("/{id}/delete", name="admin_content_delete")
     * @Method("DELETE")
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Content $content)
    {
        $em = $this->getEntityManager();

        try {
            // First, remove all associations of the group
            if ($content instanceof GroupContent) {
                /** @var ContentInGroup $item */
                foreach ($content->getItems() as $item) {
                    $em->remove($item);
                }
            }

            $em->remove($content);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "errors" => [
                        "db" => $e->getMessage()
                    ],
                    "message" => "Could not delete.",
                ]
            );
        }

        return new JsonResponse(
            [
                "message" => "Content successfully deleted.",
                "location" => $this->generateUrl("admin_content_tabs"),
            ],
            302
        );
    }
}