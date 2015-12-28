<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.12.15
 * Time: 18:19
 */

namespace AdminBundle\Controller;


use AdminBundle\Form\User\UserType;
use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @Route("/admin/user")
 */
class UserController extends BaseAdminController
{

    /**
     * @Route("", name="admin_user_index")
     * @Route("/")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getEntityManager();
        $users = $entityManager->getRepository("AppBundle:User")->findAll();

        return $this->render(
            ":AdminBundle/User:index.html.twig",
            ["users" => $users,]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_user_tabs")
     * @Method("GET")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(User $user)
    {
        return $this->render(
            ':AdminBundle/User:tabs.html.twig',
            ["user" => $user,]
        );
    }


    /**
     * @Route("/new", name="admin_user_new")
     * @Method("GET")
     *
     */
    public function newAction()
    {
        $user = new User();
        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $user,
                new UserType(),
                "admin_user"
            );

        return $this->render(
            ':AdminBundle/User:new.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $em = $this->getEntityManager();

        $productForm = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $user,
                new UserType(),
                "admin_user"
            );

        $productForm->handleRequest($request);

        if ($productForm->isValid()) {
            $em->persist($user);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'errors' => ['db' => $e->getMessage(),]
                    ]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Product successfully created",
                    "location" => $this->generateUrl("admin_user_tabs", ["id" => $user->getId()]),
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * @Route("/edit/{id}", name="admin_user_edit")
     * @Method("GET")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(User $user)
    {
        $form = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $user,
                new UserType(),
                "admin_user",
                ["id" => $user->getId(),]
            );

        return $this->render(
            ':AdminBundle/User:edit.html.twig',
            [
                "user" => $user,
                "form" => $form->createView(),
            ]
        );
    }


    /**
     * @Route("/update/{id}", name="admin_user_update")
     * @Method("PUT")
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, User $user)
    {
        $em = $this->getEntityManager();

        $form = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $user,
                new UserType(),
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
                        "errors" => ["db" => $e->getMessage(),]
                    ]
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
     * @Route("/tab/{id}/delete", name="admin_user_delete_tab")
     *
     * @param User $user
     *
     * @return Response
     *
     */
    public function deleteTabAction(User $user)
    {
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_user", $user->getId());

        return $this
            ->render(
                ":AdminBundle/User:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * @Route("/{id}/delete", name="admin_user_delete")
     *
     * @param Request $request
     * @param User $user
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        try {
            $em = $this->getEntityManager();
            $em->remove($user);
            $em->flush();
        } catch (DBALException $e) {
            return new JsonResponse(
                [
                    "errors" => ["db" => $e->getMessage(),],
                    "message" => "Could not delete.",
                ]
            );
        }

        return new JsonResponse(
            [
                "message" => "User successfully deleted.",
                "location" => $this->generateUrl("admin_user_index"),
            ],
            302
        );
    }

}