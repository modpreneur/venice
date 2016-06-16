<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace Venice\AdminBundle\Controller;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\BillingPlanType;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BillingPlanController
 * @package Venice\AdminBundle\Controller
 */
class BillingPlanController extends BaseAdminController
{

    /**
     * Get information about billing plan of given product.
     *
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return array
     */
    public function indexAction(Request $request, StandardProduct $product)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $billingPlans = $entityManager->getRepository("VeniceAppBundle:BillingPlan")->findBy(["product" => $product]);

        $necktieBillingPlanShowUrl = $this->getParameter("necktie_show_billing_plan_url");

        return $this->render(
            "VeniceAdminBundle:BillingPlan:index.html.twig",
            [
                "billingPlans" => $billingPlans,
                "product" => $product,
                "necktieUrl" => $necktieBillingPlanShowUrl
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
            "VeniceAdminBundle:BillingPlan:show.html.twig",
            ["billingPlan" => $billingPlan]
        );
    }


    /**
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

        $necktieBillingPlanShowUrl = $this->getParameter("necktie_show_product_url");
        $necktieBillingPlanShowUrl = str_replace(":id", $billingPlan->getNecktieId(), $necktieBillingPlanShowUrl);

        return $this->render(
            "VeniceAdminBundle:BillingPlan:tabs.html.twig",
            [
                "billingPlan" => $billingPlan,
                "necktieBillingPlanShowUrl" => $necktieBillingPlanShowUrl
            ]
        );
    }


    /**
     * Set billing plan as default for product.
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

        $billingPlan->getProduct()->setDefaultBillingPlan($billingPlan);

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
     * @Security("is_granted('ROLE_ADMIN_BILLING_PLAN_EDIT')")
     *
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
            "VeniceAdminBundle:BillingPlan:edit.html.twig",
            [
                "entity" => $billingPlan,
                "form" => $billingPlanForm->createView(),
            ]

        );
    }


    /**
     * Process a request to update a BillingPlan entity.
     *
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
            $priceGenerator = $this->get("trinity.services.price_string_generator");
            $billingPlan->setPrice($priceGenerator->generateFullPriceStr($billingPlan));

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
            'VeniceAdminBundle:BillingPlan:new.html.twig',
            [
                'billingPlan' => $billingPlan,
                'form' => $form->createView(),
            ]
        );
    }


    /**
     * Process a request to create a new BillingPlan entity.
     *
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
            $priceGenerator = $this->get("trinity.services.price_string_generator");
            $billingPlan->setPrice($priceGenerator->generateFullPriceStr($billingPlan));

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
                "VeniceAdminBundle:BillingPlan:tabDelete.html.twig",
                ["form" => $form->createView(),]
            );
    }


    /**
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
//todo: variable $product
        return new JsonResponse(
            [
                "message" => "BillingPlan successfully deleted.",
                "location" => $this->generateUrl("admin_product_tabs", ["id" => $product->getId()])."#tab4",
            ],
            302
        );
    }

}