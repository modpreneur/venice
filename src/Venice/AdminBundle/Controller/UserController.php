<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.12.15
 * Time: 18:19.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Form\User\RolesType;

/**
 * Class UserController.
 */
class UserController extends BaseAdminController
{
    /**
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \LogicException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     */
    public function indexAction()
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Users', 'admin_user_index');

        $count = $this->getDoctrine()->getRepository('VeniceAppBundle:User')->count();

        $url = $this->generateUrl('grid_default', ['entity' => 'User']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $count
        );

        $gridConfBuilder->addColumn('id', 'Id');
        $gridConfBuilder->addColumn('username', 'User Name');
        $gridConfBuilder->addColumn('email', 'Email');
        $gridConfBuilder->addColumn('fullName', 'Full Name');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        return $this->render(
            'VeniceAdminBundle:User:index.html.twig',
            ['gridConfiguration' => $gridConfBuilder->getJSON(), 'count' => $count]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @param User $user
     *
     * @return Response
     */
    public function showAction(User $user)
    {
        return $this->render(
            'VeniceAdminBundle:User:show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_VIEW')")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(User $user)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Users', 'admin_user_index')
            ->addRouteItem($user->getFullNameOrUsername(), 'admin_user_tabs', ['id' => $user->getId()]);

        $necktieUserShowUrl = null;

        $necktieUserShowUrl = $this->getParameter('necktie_show_user_url');
        $necktieUserShowUrl = str_replace(':id', $user->getNecktieId(), $necktieUserShowUrl);

        return $this->render(
            'VeniceAdminBundle:User:tabs.html.twig',
            [
                'user' => $user,
                'necktieUserShowUrl' => $necktieUserShowUrl,
            ]
        );
    }

//    /**
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
//     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
//     */
//    public function createAction(Request $request)
//    {
//        $user = new User();
//        $entityManager = $this->getEntityManager();
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
//            $entityManager->persist($user);
//
//            try {
//                $entityManager->flush();
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
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function editAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                $this->getEntityFormMatcher()->getFormClassForEntity($user),
                'admin_user',
                ['id' => $user->getId()]
            );

        return $this->render(
            'VeniceAdminBundle:User:edit.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \RuntimeException
     */
    public function updateAction(Request $request, User $user)
    {
        $entityManager = $this->getEntityManager();

        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                $this->getEntityFormMatcher()->getFormClassForEntity($user),
                'admin_user',
                ['id' => $user->getId()]
            );

        $originalPassword = $user->getPassword();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if (!empty($plainPassword)) {
                //encode the password
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $tempPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($tempPassword);
            } else {
                $user->setPassword($originalPassword);
            }

            $entityManager->persist($user);

            try {
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'error' => ['db' => $e->getMessage()],
                    ],
                    400
                );
            }

            return new JsonResponse(
                [
                    'message' => 'User successfully updated',
                ]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function deleteTabAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_user', $user->getId());

        return $this
            ->render(
                'VeniceAdminBundle:User:tabDelete.html.twig',
                ['form' => $form->createView()]
            );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param Request $request
     * @param User    $user
     *
     * @return JsonResponse
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm('admin_user', $user->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            try {
                $entityManager = $this->getEntityManager();
                $entityManager->remove($user);
                $entityManager->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        'error' => ['db' => $e->getMessage()],
                        'message' => 'Could not delete.',
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                'message' => 'User successfully deleted.',
                'location' => $this->generateUrl('admin_user_index'),
            ],
            302
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User $user
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function rolesEditAction(User $user)
    {
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                $this->getFormOverrideHandler()->getFormClass(RolesType::class),
                'admin_user_roles',
                ['id' => $user->getId()]
            );

        return $this->render(
            'VeniceAdminBundle:User:roles.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_USER_EDIT')")
     *
     * @param User    $user
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Form\Exception\LogicException
     */
    public function rolesUpdateAction(Request $request, User $user)
    {
        $userManager = $this->get('fos_user.user_manager');
        $form = $this->getFormCreator()
            ->createEditForm(
                $user,
                $this->getFormOverrideHandler()->getFormClass(RolesType::class),
                'admin_user_roles',
                ['id' => $user->getId()]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $userManager->updateUser($user);

            return new JsonResponse(
                [
                    'message' => 'Permissions successfully updated.',
                ]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }
}
