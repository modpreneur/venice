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
use Trinity\NotificationBundle\Notification\NotificationParser;

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
        $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                json_decode($request->getContent(), true),
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
        $updatedBillingPlan = $this->get("trinity.notification.services.notification_parser")
            ->parseNotification(
                $request->request->all(),
                "AppBundle\\Entity\\BillingPlan",
                $request->getMethod(),
                $this->getParameter("necktie_client_secret")
            )
        ;

        // The parser returns updated entity. In this case we have to set the updated billing plan to the product.
        $product = $updatedBillingPlan->getProduct();
        $product->setBillingPlan($updatedBillingPlan);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

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