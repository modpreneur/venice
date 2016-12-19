<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\BillingPlan;
//use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class BillingPlanController.
 */
class BillingPlanController extends BaseAdminController
{
    /**
     * Get information about billing plan of given product.
     *
     * @param int $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws \LogicException
     * @throws \Trinity\Bundle\GridBundle\Exception\DuplicateColumnException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     */
    public function indexAction(Request $request, int $id)
    {
        $count = $this->getDoctrine()->getRepository('VeniceAppBundle:BillingPlan')->countByProduct($id);
        $url = $this->generateUrl('grid_default', ['entity' => 'BillingPlan']);

        $gridConfBuilder = $this->get('trinity.grid.grid_configuration_service')->createGridConfigurationBuilder(
            $url,
            $count
        );
        // Defining columns
        $gridConfBuilder->addColumn('id', '#');
        $gridConfBuilder->addColumn('necktieDefault', 'Default on Necktie');
        $gridConfBuilder->addColumn('veniceDefault', 'Default on Venice');
        $gridConfBuilder->addColumn('type', 'Type');
        $gridConfBuilder->addColumn('initialPrice', 'Price');
        $gridConfBuilder->addColumn('frequency', 'Frequency');
        $gridConfBuilder->addColumn('trial', 'Trial');
        $gridConfBuilder->addColumn('details', ' ', ['allowOrder' => false]);

        $gridConfBuilder->setProperty('filter', 'product='.$id);

        return $this->render(
            'VeniceAdminBundle:BillingPlan:index.html.twig',
            [
                'count' => $count,
                'gridConfiguration' => $gridConfBuilder->getJSON(),
            ]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_VIEW')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return Response
     */
    public function showAction(Request $request, BillingPlan $billingPlan)
    {
        return $this->render(
            'VeniceAdminBundle:BillingPlan:show.html.twig',
            ['billingPlan' => $billingPlan]
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_VIEW')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return Response
     */
    public function tabsAction(Request $request, BillingPlan $billingPlan)
    {
        $this->getBreadcrumbs()
            ->addRouteItem('Products', 'admin_product_index')
            ->addRouteItem(
                $billingPlan->getProduct()->getName(),
                'admin_product_tabs',
                ['id' => $billingPlan->getProduct()->getId()]
            )
            ->addRouteItem(
                'Billing plan '.$billingPlan->getId(),
                'admin_billing_plan_tabs',
                ['id' => $billingPlan->getId()]
            );

        $necktieBillingPlanShowUrl = $this->getParameter('necktie_show_product_url');
        $necktieBillingPlanShowUrl = str_replace(':id', $billingPlan->getNecktieId(), $necktieBillingPlanShowUrl);

        return $this->render(
            'VeniceAdminBundle:BillingPlan:tabs.html.twig',
            [
                'billingPlan' => $billingPlan,
                'necktieBillingPlanShowUrl' => $necktieBillingPlanShowUrl,
            ]
        );
    }

    /**
     * Set billing plan as default for product.
     *
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \LogicException
     */
    public function setDefaultBillingPlanAction(Request $request, BillingPlan $billingPlan)
    {
        $unset = $request->request->has('unset');
        $entityManager = $this->getDoctrine()->getManager();

        $billingPlan->getProduct()->setVeniceDefaultBillingPlan($unset ? null : $billingPlan);

        try {
            $entityManager->persist($billingPlan);
            $entityManager->persist($billingPlan->getProduct());
            $entityManager->flush();
        } catch (DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());

            return new JsonResponse(
                [
                    'status' => 'error',
                    'error' => ['db' => $e->getMessage()],
                    'message' => 'Default billing plan could not be changed.',
                ],
                400
            );
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Successfully changed'], 200);
    }

//    /**
//     * Display a form to edit a BillingPlan entity.
//     *
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param Request $request
//     * @param BillingPlan $billingPlan
//     *
//     * @return Response
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     * @throws \LogicException
//     */
//    public function editAction(Request $request, BillingPlan $billingPlan)
//    {
//        $billingPlanForm = $this->getFormCreator()
//            ->createEditForm(
//                $billingPlan,
//                $this->getEntityFormMatcher()->getFormClassForEntity($billingPlan),
//                'admin_billing_plan',
//                ['id' => $billingPlan->getId()],
//                ['product' => $billingPlan->getProduct()]
//            );
//
//        return $this->render(
//            'VeniceAdminBundle:BillingPlan:edit.html.twig',
//            [
//                'entity' => $billingPlan,
//                'form' => $billingPlanForm->createView(),
//            ]
//        );
//    }

//    /**
//     * Process a request to update a BillingPlan entity.
//     *
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param Request     $request
//     * @param BillingPlan $billingPlan
//     *
//     * @return JsonResponse
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     * @throws \LogicException
//     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
//     */
//    public function updateAction(Request $request, BillingPlan $billingPlan)
//    {
//        $entityManager = $this->getEntityManager();
//
//        $billingPlanForm = $this->getFormCreator()
//            ->createEditForm(
//                $billingPlan,
//                $this->getEntityFormMatcher()->getFormClassForEntity($billingPlan),
//                'admin_billing_plan',
//                ['id' => $billingPlan->getId()],
//                ['product' => $billingPlan->getProduct()]
//            );
//
//        $billingPlanForm->handleRequest($request);
//
//        if ($billingPlanForm->isValid()) {
//            $priceGenerator = $this->get('trinity.services.price_string_generator');
//            $billingPlan->setPrice($priceGenerator->generateFullPriceStr($billingPlan));
//
//            $entityManager->persist($billingPlan);
//
//            try {
//                $entityManager->flush();
//            } catch (DBALException $e) {
//                return new JsonResponse(
//                    ['error' => ['db' => $e->getMessage()]],
//                    400
//                );
//            }
//
//            return new JsonResponse(
//                ['message' => 'BillingPlan successfully updated']
//            );
//        } else {
//            return $this->returnFormErrorsJsonResponse($billingPlanForm);
//        }
//    }

//    /**
//     * Display a form to create a new BillingPlan entity.
//     *
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param Request $request
//     * @param StandardProduct $product
//     *
//     * @return Response
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     * @throws \LogicException
//     */
//    public function newAction(Request $request, StandardProduct $product)
//    {
//        $this->getBreadcrumbs()
//            ->addRouteItem('Products', 'admin_product_index')
//            ->addRouteItem(
//                $product->getName(),
//                'admin_product_tabs',
//                ['id' => $product->getId()]
//            )
//            ->addRouteItem(
//                'New billing plan',
//                'admin_billing_plan_new',
//                ['id' => $product->getId()]
//            );
//
//        $billingPlan = $this->getEntityOverrideHandler()->getEntityInstance(BillingPlan::class);
//
//        $form = $this->getFormCreator()
//            ->createCreateForm(
//                $billingPlan,
//                $this->getEntityFormMatcher()->getFormClassForEntity($billingPlan),
//                'admin_billing_plan',
//                ['id' => $product->getId()],
//                ['product' => $product]
//            );
//
//        return $this->render(
//            'VeniceAdminBundle:BillingPlan:new.html.twig',
//            [
//                'billingPlan' => $billingPlan,
//                'form' => $form->createView(),
//            ]
//        );
//    }

//    /**
//     * Process a request to create a new BillingPlan entity.
//     *
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param Request         $request
//     * @param StandardProduct $product
//     *
//     * @return JsonResponse
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     * @throws \LogicException
//     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
//     */
//    public function createAction(Request $request, StandardProduct $product)
//    {
//        $entityManager = $this->getEntityManager();
//        $billingPlan = $this->getEntityOverrideHandler()->getEntityInstance(BillingPlan::class);
//
//        $form = $this->getFormCreator()
//            ->createCreateForm(
//                $billingPlan,
//                $this->getEntityFormMatcher()->getFormClassForEntity($billingPlan),
//                'admin_billing_plan',
//                ['id' => $product->getId()],
//                ['product' => $product]
//            );
//
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $priceGenerator = $this->get('trinity.services.price_string_generator');
//            $billingPlan->setPrice($priceGenerator->generateFullPriceStr($billingPlan));
//
//            $entityManager->persist($billingPlan);
//
//            try {
//                $entityManager->flush();
//            } catch (DBALException $e) {
//                return new JsonResponse(
//                    ['errors' => ['db' => $e->getMessage()]]
//                );
//            }
//
//            return new JsonResponse(
//                [
//                    'message' => 'Billing plan successfully created',
//                    'location' => $this->generateUrl('admin_product_tabs', ['id' => $product->getId()]).'#tab4',
//                ],
//                302
//            );
//        } else {
//            return $this->returnFormErrorsJsonResponse($form);
//        }
//    }

//    /**
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param BillingPlan $billingPlan
//     *
//     * @return Response
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     */
//    public function deleteTabAction(BillingPlan $billingPlan)
//    {
//        $form = $this->getFormCreator()
//            ->createDeleteForm('admin_billing_plan', $billingPlan->getId());
//
//        return $this
//            ->render(
//                'VeniceAdminBundle:BillingPlan:tabDelete.html.twig',
//                ['form' => $form->createView()]
//            );
//    }

//    /**
//     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
//     *
//     * @param Request     $request
//     * @param BillingPlan $billingPlan
//     *
//     * @return JsonResponse
//     *
//     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
//     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
//     * @throws \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
//     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
//     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
//     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
//     * @throws \Symfony\Component\Form\Exception\LogicException
//     * @throws \LogicException
//     */
//    public function deleteAction(Request $request, BillingPlan $billingPlan)
//    {
//        $form = $this->getFormCreator()
//            ->createDeleteForm('admin_billing_plan', $billingPlan->getId());
//
//        $form->handleRequest($request);
//
//        $product = $billingPlan->getProduct();
//        if ($form->isValid()) {
//            try {
//                $entityManager = $this->getEntityManager();
//                $entityManager->remove($billingPlan);
//                $entityManager->flush();
//            } catch (DBALException $e) {
//                return new JsonResponse(
//                    [
//                        'error' => ['db' => $e->getMessage()],
//                        'message' => 'Could not delete.',
//                    ],
//                    400
//                );
//            }
//        }
//
//        return new JsonResponse(
//            [
//                'message' => 'BillingPlan successfully deleted.',
//                'location' => $this->generateUrl('admin_product_tabs', ['id' => $product->getId()]).'#tab4',
//            ],
//            302
//        );
//    }
}
