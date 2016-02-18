<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.11.15
 * Time: 11:59
 */

namespace AppBundle\Controller\Notification;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Trinity\NotificationBundle\Annotations\DisableNotification;

/**
 * @DisableNotification()
 *
 * Class NotificationController
 * @package AppBundle\Controller\Notification
 */
class NotificationController extends Controller
{
    /**
     * @Route("/notify/product", defaults={"_format": "json"})
     * @Route("/notify/product/", defaults={"_format": "json"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function productAction(Request $request)
    {
        $this->get("logger")->addEmergency("product_notification_body: ".json_encode($request->request->all()));

        $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                $request->request->all(),
                "AppBundle\\Entity\\Product\\StandardProduct",
                $request->getMethod(),
                $this->getParameter("necktie_client_secret")
            )
        ;

        return new JsonResponse("ok");
    }

    /**
     * @Route("/notify/user", defaults={"_format": "json"})
     * @Route("/notify/user/", defaults={"_format": "json"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function userAction(Request $request)
    {
        $this->get("logger")->addEmergency("user_notification_body: ".json_encode($request->request->all()));

        $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                $request->request->all(),
                "AppBundle\\Entity\\User",
                $request->getMethod(),
                $this->getParameter("necktie_client_secret")
            )
        ;

        return new JsonResponse("ok");
    }

    /**
     * @Route("/notify/billing-plan", defaults={"_format": "json"})
     * @Route("/notify/billing-plan/", defaults={"_format": "json"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function billingPlanAction(Request $request)
    {
        $this->get("logger")->addEmergency("billing_plan_notification_body: ".json_encode($request->request->all()));

        $updatedBillingPlan = $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                $request->request->all(),
                "AppBundle\\Entity\\BillingPlan",
                $request->getMethod(),
                $this->getParameter("necktie_client_secret")
            )
        ;

        if("DELETE" !== $request->getMethod()) {
            // Generate price string
            $priceGenerator = $this->get("trinity.services.price_string_generator");
            $updatedBillingPlan->setPrice($priceGenerator->generateFullPriceStr($updatedBillingPlan));

            // The parser returns updated entity. In this case we have to set the updated billing plan to the product.
            $product = $updatedBillingPlan->getProduct();
            $product->setBillingPlan($updatedBillingPlan);
            $updatedBillingPlan->generateAndSetPriceString();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->persist($updatedBillingPlan);
            $em->flush();
        }

        return new JsonResponse("ok");
    }

    /**
     * @Route("/notify/product-access", defaults={"_format": "json"})
     * @Route("/notify/product-access/", defaults={"_format": "json"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function productAccessAction(Request $request)
    {
        $this->get("logger")->addEmergency("product_access_notification_body: ".json_encode($request->request->all()));

        $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                $request->request->all(),
                "AppBundle\\Entity\\ProductAccess",
                $request->getMethod(),
                $this->getParameter("necktie_client_secret")
            )
        ;

        return new JsonResponse("ok");
    }
}