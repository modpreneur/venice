<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.12.15
 * Time: 18:19
 */

namespace Venice\AdminBundle\Controller;


use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Form\User\RolesType;
use Venice\AppBundle\Form\User\UserType;
use Venice\AppBundle\Services\RolesManager;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 */
class UserController extends BaseAdminController
{

    /**
     * @Route("/admin/user", name="admin_user_index")
     * @Route("/admin/user/")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Users", "admin_user_index");

        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository("VeniceAppBundle:User")->findAll();

        return $this->render(
            "VeniceAdminBundle:User:index.html.twig",
            ["users" => $users,]
        );
    }


    /**
     * @Route("/admin/user/show/{id}", name="admin_user_show")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function showAction(Request $request, User $user)
    {
        return $this->render(
            "VeniceAdminBundle:User:show.html.twig",
            ["user" => $user,]
        );
    }


    /**
     * @Route("/admin/user/tabs/{id}", name="admin_user_tabs")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(User $user)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Users", "admin_user_index")
            ->addRouteItem($user->getFullNameOrUsername(), "admin_user_tabs", ["id" => $user->getId()]);

        $necktieUserShowUrl = null;

        $necktieUserShowUrl = $this->getParameter("necktie_url").$this->getParameter("necktie_show_user_url");
        $necktieUserShowUrl = str_replace(":id", $user->getNecktieId(), $necktieUserShowUrl);

        return $this->render(
            'VeniceAdminBundle:User:tabs.html.twig',
            [
                "user" => $user,
                "necktieUserShowUrl" => $necktieUserShowUrl
            ]
        );
    }


//    /**
//     * @Route("/admin/user/new", name="admin_user_new")
//     * @Method("GET")
//     *
//     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
//     */
//    public function newAction()
//    {
//        $this->getBreadcrumbs()
//            ->addRouteItem("Users", "admin_user_index")
//            ->addRouteItem("New user", "admin_user_new");
//
//        $user = new User();
//        $form = $this->getFormCreator()
//            ->createCreateForm(
//                $user,
//                UserType::class,
//                "admin_user"
//            );
//
//        return $this->render(
//            'VeniceAdminBundle:User:new.html.twig',
//            [
//                'user' => $user,
//                'form' => $form->createView(),
//            ]
//        );
//    }


//    /**
//     * @Route("/admin/user/create", name="admin_user_create")
//     * @Method("POST")
//     *
//     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
//     */
//    public function createAction(Request $request)
//    {
//        $user = new User();
//        $em = $this->getEntityManager();
//
//        $productForm = $this->getFormCreator()
//            ->createCreateForm(
//                $user,
//                UserType::class,
//                "admin_user"
//            );
//
//        $productForm->handleRequest($request);
//
//        if ($productForm->isValid()) {
//            $em->persist($user);
//
//            try {
//                $em->flush();
//            } catch (DBALException $e) {
//                return new JsonResponse(
//                    [
//                        'error' => ['db' => $e->getMessage(),]
//                    ],
//                    400);
//            }
//
//            return new JsonResponse(
//                [
//                    "message" => "Product successfully created",
//                    "location" => $this->generateUrl("admin_user_tabs", ["id" => $user->getId()]),
//                ],
//                302
//            );
//        } else {
//            return $this->returnFormErrorsJsonResponse($productForm);
//        }
//    }


    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                UserType::class,
                "admin_user",
                ["id" => $user->getId(),]
            );

        return $this->render(
            'VeniceAdminBundle:User:edit.html.twig',
            [
                "user" => $user,
                "form" => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/admin/user/update/{id}", name="admin_user_update")
     * @Method("PUT")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, User $user)
    {
        $em = $this->getEntityManager();

        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                UserType::class,
                "admin_user",
                ["id" => $user->getId(),]
            );

        $originalPassword = $user->getPassword();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $form->get("plainPassword")->getData();

            if (!empty($plainPassword)) {
                //encode the password
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $tempPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($tempPassword);
            } else {
                $user->setPassword($originalPassword);
            }

            $em->persist($user);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage(),]
                    ],
                    400
                );
            }

            return new JsonResponse(
                [
                    "message" => "User successfully updated",
                ]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * @Route("/admin/user/tab/{id}/delete", name="admin_user_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     *
     */
    public function deleteTabAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_user", $user->getId());

        return $this
            ->render(
                "VeniceAdminBundle:User:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     * @Method("DELETE")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_user", $user->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $em = $this->getEntityManager();
                $em->remove($user);
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
                "message" => "User successfully deleted.",
                "location" => $this->generateUrl("admin_user_index"),
            ],
            302
        );
    }

    /**
     * @Route("/admin/user/{id}/roles/edit", name="admin_user_roles_edit")
     * @Method("GET")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     */
    public function rolesEditAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                RolesType::class,
                "admin_user_roles",
                ["id" => $user->getId()]
            );

        return $this->render(
            "VeniceAdminBundle:User:roles.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }


    /**
     * @Route("/admin/user/{id}/roles/edit", name="admin_user_roles_update")
     * @Method("PUT")
     *
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     */
    public function rolesUpdateAction(Request $request, User $user)
    {
        $userManager = $this->get("fos_user.user_manager");
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                RolesType::class,
                "admin_user_roles",
                ["id" => $user->getId()]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager->updateUser($user);

            return new JsonResponse(
                [
                    "message" => "Permissions successfully updated.",
                ]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }
}