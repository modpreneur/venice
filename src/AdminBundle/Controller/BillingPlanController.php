<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace AdminBundle\Controller;

use AdminBundle\Form\BillingPlanType;
use AppBundle\Entity\BillingPlan;
use AppBundle\Entity\Product\StandardProduct;
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

        $connectedToNecktie = $this->container->getParameter("necktie_url") !== null;
        //If not connected to necktie allow adding new billing plans

        return $this->render(
            ":AdminBundle/BillingPlan:index.html.twig",
            [
                "billingPlans" => $billingPlans,
                "product" => $product,
                "allowAddingNewBillingPlans" => !$connectedToNecktie,
                "displayAmemberField" => !$connectedToNecktie,
                "displayNecktieField" => $connectedToNecktie,
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
            ":AdminBundle/BillingPlan:show.html.twig",
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

        $connectedToNecktie = $this->container->getParameter("necktie_url") !== null;

        return $this->render(
            ":AdminBundle/BillingPlan:tabs.html.twig",
            [
                "billingPlan" => $billingPlan,
                "displayEditTab" => !$connectedToNecktie,
                "displayDeleteTab" => !$connectedToNecktie
            ]
        );
    }


    /**
     * Set billing plan as desktop billing plan for product.
     *
     * @Route("/set-{purpose}/{id}", name="admin_billing_plan_product_set")
     *
     * @Security("is_granted('ROLE_ADMIN_PRODUCT_EDIT')")
     *
     * @param Request $request
     * @param BillingPlan $billingPlan
     * @param $purpose
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function setDefaultBPAction(Request $request, $purpose, BillingPlan $billingPlan)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $billingPlan->getProduct();
        if ($purpose === "desktop") {
            $product->setDesktopBillingPlan($billingPlan);
        } else if ($purpose === "mobile") {
            $product->setMobileBillingPlan($billingPlan);
        } else {
            return new JsonResponse(["error" => ["purpose has to be 'desktop' or 'mobile'"]]);
        }

        try {
            $em->persist($product);
            $em->flush();
        } catch (DBALException $e) {
            $this->get('session')->getFlashBag()->add('danger', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('admin_product_tabs',
                ['id' => $product->getId()])."#tab3");
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
        $billingPlanForm = $this->get("admin.form_factory")
            ->createEditForm($this,
                $billingPlan,
                new BillingPlanType($billingPlan->getProduct(), $this->getDoctrine()->getManager()),
                "admin_billing_plan",
                ["id" => $billingPlan->getId(),]
            );

        return $this->render(
            ":AdminBundle/BillingPlan:edit.html.twig",
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

        $billingPlanForm = $this->get("admin.form_factory")
            ->createEditForm(
                $this,
                $billingPlan,
                new BillingPlanType($billingPlan->getProduct(), $em),
                "admin_product",
                ["id" => $billingPlan->getId()]
            );

        $billingPlanForm->handleRequest($request);

        if ($billingPlanForm->isValid()) {
            $billingPlan->generateAndSetPriceString();
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
//        $this->getBreadcrumbs()
//            ->addRouteItem("Products", "admin_product_index")
//            ->addRouteItem("New product", "admin_product_new", ["productType" => $productType]);
        $billingPlan = new BillingPlan();

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $billingPlan,
                new BillingPlanType($product, $this->getEntityManager()),
                "admin_billing_plan",
                ["id" => $product->getId(),]
            );

        return $this->render(
            ':AdminBundle/BillingPlan:new.html.twig',
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
        $type = new BillingPlanType($product, $this->getEntityManager());

        $form = $this->get("admin.form_factory")
            ->createCreateForm(
                $this,
                $billingPlan,
                $type,
                "admin_billing_plan",
                ["id" => $product->getId(),]
            );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $billingPlan->generateAndSetPriceString();
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
                    "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()])."#tab3",
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
        $form = $this->get("admin.form_factory")
            ->createDeleteForm($this, "admin_billing_plan", $billingPlan->getId());

        return $this
            ->render(
                ":AdminBundle/BillingPlan:tabDelete.html.twig",
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

        return new JsonResponse(
            [
                "message" => "BillingPlan successfully deleted.",
                "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()])."#tab3",
            ],
            302
        );
    }

}