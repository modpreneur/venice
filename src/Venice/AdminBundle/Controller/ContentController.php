<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 07.11.15
 * Time: 13:43
 */

namespace Venice\AdminBundle\Controller;


use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Content\ContentInGroup;
use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Entity\Content\VideoContent;
use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Form\ContentProduct\ContentProductTypeWithHiddenContent;
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
 * Class ContentController
 * @package Venice\AdminBundle\Controller
 */
class ContentController extends BaseAdminController
{

    /**
     * @Route("/admin/content", name="admin_content_index")
     * @Route("/admin/content/")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_VIEW')")
     *
     * @param Request $request
     *
     * @return string
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Contents", "admin_content_index");

        $contents = $this->getEntityManager()->getRepository("VeniceAppBundle:Content\\Content")->findAll();

        return $this->render(
            "VeniceAdminBundle:Content:index.html.twig",
            ["contents" => $contents,]
        );
    }


    /**
     * @Route("/admin/content/{id}/content-product", name="admin_content_content_product_index")
     *
     * @param Content $content
     *
     * @return Response
     */
    public function indexForContentAction(Content $content)
    {
        $contentProducts = $this
            ->getEntityManager()
            ->getRepository("VeniceAppBundle:ContentProduct")
            ->findBy(["content" => $content]);

        return $this->render(
            "VeniceAdminBundle:ContentProduct:contentIndex.html.twig",
            ["contentProducts" => $contentProducts,]
        );
    }


    /**
     * @Route("/admin/content/show/{id}", name="admin_content_show")
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
            "VeniceAdminBundle:Content:show".ucfirst($content->getType()).".html.twig",
            ["content" => $content]
        );
    }


    /**
     * @Route("/admin/content/tabs/{id}", name="admin_content_tabs")
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
        $this->getBreadcrumbs()
            ->addRouteItem("Contents", "admin_content_index")
            ->addRouteItem($content->getName(), "admin_content_tabs", ["id" => $content->getId()]);

        return $this->render("VeniceAdminBundle:Content:tabs.html.twig", ["content" => $content,]);
    }


    /**
     * @Route("/admin/content/new", name="admin_content_new")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Contents", "admin_content_index")
            ->addRouteItem("New content", "admin_content_new");

        $formOptions = [];

        $content = new VideoContent();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $content->getFormTypeClass(),
                "admin_content",
                ["contentType" => $content->getType()],
                $formOptions
            );

        return $this->render(
            'VeniceAdminBundle:Content:new.html.twig',
            [
                'entity' => $content,
                'form' => $form->createView()
            ]
        );
    }


    /**
     * @Route("/admin/content/new/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_new_form")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param         $contentType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function newFormAction(Request $request, $contentType)
    {
        try {
            $content = Content::createContentByType($contentType);
        } catch (ReflectionException $e) {
            throw new NotFoundHttpException("Content type: ".$contentType." not found.");
        }
        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $content->getFormTypeClass(),
                "admin_content",
                ["contentType" => $contentType,]
            );

        // Remove items field for now. todo: remove it in future?
        // Explanation:
        // The ContentInGroupType requires group id to fill it in the hidden field. But when the group is creating, there is no id.
        // This could be solved by saving the group first, getting it's id and then saving it's content.
        $form->remove("items");

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
     * @Route("/admin/content/create/{contentType}",requirements={"contentType": "\w+"}, name="admin_content_create")
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

        $formOptions = [];

        if ($content instanceof GroupContent) {
            $formOptions = ["groupContent" => ($content instanceof GroupContent) ? $content : null,];
        }

        $form = $this->getFormCreator()
            ->createCreateForm(
                $content,
                $content->getFormTypeClass(),
                "admin_content",
                ["contentType" => $contentType],
                $formOptions
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
     * @Route("/admin/content/edit/{id}", requirements={"id": "\d+"}, name="admin_content_edit")
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
        $formOptions = [];

        if ($content instanceof GroupContent) {
            $formOptions = ["groupContent" => ($content instanceof GroupContent) ? $content : null,];
        }

        $form = $this->getFormCreator()
            ->createEditForm(
                $content,
                $content->getFormTypeClass(),
                "admin_content",
                ["groupContent" => $content],
                $formOptions
            );

        return $this->render(
            "VeniceAdminBundle:Content:edit.html.twig",
            [
                "entity" => $content,
                "form" => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to update a Content entity.
     *
     * @Route("/admin/content/{id}/update", requirements={"id": "\d+"}, name="admin_content_update")
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

        $form = $this->getFormCreator()
            ->createEditForm(
                $content,
                $content->getFormTypeClass(),
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
                        "error" => ["db" => $e->getMessage(),],
                    ],
                    400
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
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $content,
                $content->getFormTypeClass(),
                "admin_content",
                ["groupContent" => $content,]
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
                        "error" => ["db" => $e->getMessage(),],
                    ],
                    400
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
     * @Route("/admin/content/tab/{id}/delete", name="admin_content_delete_tab")
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
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_content", $content->getId());

        return $this
            ->render(
                "VeniceAdminBundle:Content:tabDelete.html.twig",
                [
                    "form" => $form->createView()
                ]
            );
    }


    /**
     * @Route("/admin/content/{id}/delete", name="admin_content_delete")
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

        $form = $this->getFormCreator()
            ->createDeleteForm("admin_content", $content->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
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
                        "error" => ["db" => $e->getMessage(),],
                        "message" => "Could not delete.",
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                "message" => "Content successfully deleted.",
                "location" => $this->generateUrl("admin_content_index"),
            ],
            302
        );
    }


    /**
     * @Route("/admin/content/{id}/content-product", name="admin_content_content_product_index")
     *
     * @param Content $content
     *
     * @return Response
     */
    public function contentProductIndexAction(Content $content)
    {
        return $this->render(
            "VeniceAdminBundle:Content:contentProductIndex.html.twig",
            [
                "contentProducts" => $content->getContentProducts(),
                "content" => $content
            ]
        );
    }


    /**
     * @Route("/admin/content/{id}/content-product/new", name="admin_content_content_product_new")
     *
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param Content $content
     *
     * @return Response
     */
    public function contentProductNewAction(Request $request, Content $content)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_content_index")
            ->addRouteItem(
                $content->getName(),
                "admin_content_tabs",
                ["id" => $content->getId()]
            )
            ->addRouteItem(
                "New association",
                "admin_content_content_product_new",
                ["id" => $content->getId()]
            );

        $form = $this->getFormCreator()
            ->createCreateForm(
                new ContentProduct(),
                ContentProductTypeWithHiddenContent::class,
                "admin_content_content_product",
                [],
                ["content" => $content,]
            );

        return $this->render("VeniceAdminBundle:ContentProduct:new.html.twig",
            ["form" => $form->createView(),]
        );
    }


    /**
     * @Route("/admin/content/content-product/new", name="admin_content_content_product_create")
     * @Method("POST")
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contentProductCreateAction(Request $request)
    {
        $contentProduct = new ContentProduct();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $contentProduct,
                ContentProductTypeWithHiddenContent::class,
                "admin_content_content_product"
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contentProduct);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),],
                    ],
                    400
                );
            }

            $url = $this->generateUrl(
                    "admin_content_tabs",
                    ["id" => $contentProduct->getContent()->getId()]
                )."#tab3";

            return new JsonResponse(
                [
                    "message" => "Successfully created",
                    "location" => $url
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }

    }


    /**
     * @Route("/admin/content/content-product/edit/{id}", requirements={"id": "\d+"}, name="admin_content_content_product_edit")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function contentProductEditAction(ContentProduct $contentProduct)
    {
        $contentForm = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                ContentProductTypeWithHiddenContent::class,
                "admin_content_content_product",
                ["id" => $contentProduct->getId(),],
                ["content" => $contentProduct->getContent()]
            );

        return $this->render(
            "VeniceAdminBundle:ContentProduct:edit.html.twig",
            [
                "entity" => $contentProduct,
                "form" => $contentForm->createView(),
            ]
        );
    }


    /**
     * @Route("/admin/content/content-product/{id}/update", requirements={"id": "\d+"}, name="admin_content_content_product_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     *
     */
    public function contentProductUpdateAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $contentProduct,
                ContentProductTypeWithHiddenContent::class,
                "admin_content_content_product",
                ["id" => $contentProduct->getId(),],
                ["content" => $contentProduct->getContent()]
            );

        $em = $this->getEntityManager();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($contentProduct);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),],
                    ], 400
                );
            }

            return new JsonResponse(
                ["message" => "Association successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * @Route("/admin/content/content-product/tabs/{id}", name="admin_content_content_product_tabs")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_VIEW')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductTabsAction(ContentProduct $contentProduct)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Contents", "admin_content_index")
            ->addRouteItem(
                $contentProduct->getContent()->getName(),
                "admin_content_tabs",
                ["id" => $contentProduct->getContent()->getId()]
            )
            ->addRouteItem(
                "Association with ".$contentProduct->getProduct()->getName(),
                "admin_content_content_product_tabs",
                ["id" => $contentProduct->getId()]
            );

        return $this->render(
            "VeniceAdminBundle:Content:contentProductTabs.html.twig",
            ["contentProduct" => $contentProduct,]
        );
    }


    /**
     * @Route("/admin/content/content-product/tab/{id}/delete", name="admin_content_content_product_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param ContentProduct $contentProduct
     *
     * @return Response
     */
    public function contentProductDeleteTabAction(ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_content_content_product", $contentProduct->getId());

        return $this
            ->render(
                "VeniceAdminBundle:ContentProduct:tabDelete.html.twig",
                [
                    "form" => $form->createView()
                ]
            );
    }


    /**
     * @Route("/admin/content/content-product/{id}/delete", name="admin_content_content_product_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_ADMIN_CONTENT_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param ContentProduct $contentProduct
     *
     * @return JsonResponse
     */
    public function contentProductDeleteAction(Request $request, ContentProduct $contentProduct)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_content_content_product", $contentProduct->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getEntityManager();
                $em->remove($contentProduct);
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),],
                        "message" => "Could not delete.",
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                "message" => "Association successfully deleted.",
                "location" => $this->generateUrl("admin_content_tabs", ["id" => $contentProduct->getContent()->getId()]),
            ],
            302
        );
    }

}