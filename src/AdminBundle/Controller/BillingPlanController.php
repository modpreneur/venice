<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace AdminBundle\Controller;

use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Form\BillingPlanType;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/billing-plan")
 *
 * Class BillingPlanController
 * @package AdminBundle\Controller
 */
class BillingPlanController extends BaseAdminController
{

    /**
     * Get information about billing plan of given product.
     *
     * @Route("/product/{id}", name="admin_billing_plan_product_index")
     * @Method("GET")
     * @View()
     *
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return array
     */
    public function indexAction(Request $request, StandardProduct $product)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $billingPlans = $entityManager->getRepository("AppBundle:BillingPlan")->findBy(["product" => $product]);

        return $this->render(
            "AdminBundle:BillingPlan:index.html.twig",
            [
                "billingPlans" => $billingPlans,
                "product" => $product
            ]
        );
    }


    /**
     * @Route("/show/{id}", name="admin_billing_plan_show")
     * @Method("GET")
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
            "AdminBundle:BillingPlan:show.html.twig",
            ["billingPlan" => $billingPlan]
        );
    }


    /**
     * @Route("/tabs/{id}", name="admin_billing_plan_tabs")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_VIEW')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function tabsAction(Request $request, BillingPlan $billingPlan)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem(
                $billingPlan->getProduct()->getName(),
                "admin_product_tabs",
                ["id" => $billingPlan->getProduct()->getId()]
            )
            ->addRouteItem(
                "Billing plan ".$billingPlan->getId(),
                "admin_billing_plan_tabs",
                ["id" => $billingPlan->getId()]
            );

        return $this->render(
            "AdminBundle:BillingPlan:tabs.html.twig",
            [
                "billingPlan" => $billingPlan,
            ]
        );
    }


    /**
     * Set billing plan as default for product.
     *
     * @Route("/set-default/{id}", name="admin_billing_plan_product_set_default")
     *
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param BillingPlan $billingPlan
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function setDefaultBillingPlanAction(BillingPlan $billingPlan)
    {
        $em = $this->getDoctrine()->getManager();

        $billingPlan->setAsDefault();

        try {
            $em->persist($billingPlan);
            $em->persist($billingPlan->getProduct());
            $em->flush();
        } catch (DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('admin_product_tabs',
                ['id' => $billingPlan->getProduct()->getId()])."#tab4"
        );
    }


    /**
     * Display a form to edit a BillingPlan entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_billing_plan_edit")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, BillingPlan $billingPlan)
    {
        $billingPlanForm = $this->getFormCreator()
            ->createEditForm(
                $billingPlan,
                BillingPlanType::class,
                "admin_billing_plan",
                ["id" => $billingPlan->getId(),],
                ["product" => $billingPlan->getProduct()]
            );

        return $this->render(
            "AdminBundle:BillingPlan:edit.html.twig",
            [
                "entity" => $billingPlan,
                "form" => $billingPlanForm->createView(),
            ]

        );
    }


    /**
     * Process a request to update a BillingPlan entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_billing_plan_update")
     * @Method("PUT")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, BillingPlan $billingPlan)
    {
        $em = $this->getEntityManager();

        $billingPlanForm = $this->getFormCreator()
            ->createEditForm(
                $billingPlan,
                BillingPlanType::class,
                "admin_billing_plan",
                ["id" => $billingPlan->getId(),],
                ["product" => $billingPlan->getProduct()]
            );

        $billingPlanForm->handleRequest($request);

        if ($billingPlanForm->isValid()) {
            $em->persist($billingPlan);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ["error" => ["db" => $e->getMessage(),]],
                    400
                );
            }

            return new JsonResponse(
                ["message" => "BillingPlan successfully updated",]
            );
        } else {
            return $this->returnFormErrorsJsonResponse($billingPlanForm);
        }
    }


    /**
     * Display a form to create a new BillingPlan entity.
     *
     * @Route("/new/{id}", name="admin_billing_plan_new")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return Response
     */
    public function newAction(Request $request, StandardProduct $product)
    {
        $this->getBreadcrumbs()
            ->addRouteItem("Products", "admin_product_index")
            ->addRouteItem(
                $product->getName(),
                "admin_product_tabs",
                ["id" => $product->getId()]
            )
            ->addRouteItem(
                "New billing plan",
                "admin_billing_plan_new",
                ["id" => $product->getId()]
            );

        $billingPlan = new BillingPlan();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $billingPlan,
                BillingPlanType::class,
                "admin_billing_plan",
                ["id" => $product->getId(),],
                ["product" => $product]
            );

        return $this->render(
            'AdminBundle:BillingPlan:new.html.twig',
            [
                'billingPlan' => $billingPlan,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to create a new BillingPlan entity.
     *
     * @Route("/create/{id}", name="admin_billing_plan_create")
     * @Method("POST")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, StandardProduct $product)
    {
        $em = $this->getEntityManager();
        $billingPlan = new BillingPlan();

        $form = $this->getFormCreator()
            ->createCreateForm(
                $billingPlan,
                BillingPlanType::class,
                "admin_billing_plan",
                ["id" => $product->getId(),],
                ["product" => $product]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($billingPlan);

            try {
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    ['errors' => ['db' => $e->getMessage(),]]
                );
            }

            return new JsonResponse(
                [
                    "message" => "Billing plan successfully created",
                    "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()])."#tab4",
                ],
                302
            );
        } else {
            return $this->returnFormErrorsJsonResponse($form);
        }
    }


    /**
     * @Route("/tab/{id}/delete", name="admin_billing_plan_delete_tab")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
     * @param BillingPlan $billingPlan
     *
     * @return Response
     *
     */
    public function deleteTabAction(BillingPlan $billingPlan)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_billing_plan", $billingPlan->getId());

        return $this
            ->render(
                "AdminBundle:BillingPlan:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
     * @Route("/{id}/delete", name="admin_billing_plan_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, BillingPlan $billingPlan)
    {
        $form = $this->getFormCreator()
            ->createDeleteForm("admin_billing_plan", $billingPlan->getId());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $product = $billingPlan->getProduct();
            try {
                $em = $this->getEntityManager();
                $em->remove($billingPlan);
                $em->flush();
            } catch (DBALException $e) {
                return new JsonResponse(
                    [
                        "error" => ["db" => $e->getMessage()],
                        "message" => "Could not delete.",
                    ],
                    400
                );
            }
        }

        return new JsonResponse(
            [
                "message" => "BillingPlan successfully deleted.",
                "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()])."#tab4",
            ],
            302
        );
    }

}