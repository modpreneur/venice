<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 16:41
 */

namespace AdminBundle\Controller;

use AppBundle\Entity\Product\StandardProduct;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 *
 * Class BillingPlanController
 * @package AdminBundle\Controller
 */
class BillingPlanController extends BaseAdminController
{
    /**
     * Get information about billing plan from master CMS.
     *
     * @Route("/billing-plan/{id}", requirements={"id": "\d+"}, name="admin_billing_plan_show")
     * @Method("GET")
     * @View()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function showAction(Request $request, $id)
    {
        $billingPlan = $this
            ->get("app.services.connection_manager")
            ->getBillingPlan($this->getUser(), $id);

        return ["billingPlan" => $billingPlan];
    }


    /**
     * * Get information about billing plan of given product from master CMS.
     *
     * @Route("/product/{id}/billing-plans")
     * @Method("GET")
     * @View()
     *
     * @param Request         $request
     * @param StandardProduct $product
     *
     * @return array
     */
    public function indexAction(Request $request, StandardProduct $product)
    {
        $billingPlans = $this
            ->get("app.services.connection_manager")
            ->getBillingPlans($this->getUser(), $product);

        ldd($billingPlans);
        return ["billingPlans" => $billingPlans];
    }
}