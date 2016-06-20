<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 12:03
 */

namespace Venice\AppBundle\Controller;


use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NecktieBuyController
 */
class NecktieBuyController extends Controller
{
    /**
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return RedirectResponse
     */
    public function redirectToNecktieBuy(Request $request, StandardProduct $product)
    {
        $useStoredCC = $request->query->has("useStoredCC");
        $billingPlanId = $request->query->get("billingPlanId");

        /** @var User $user */
        $user = $this->getUser();

        $this->get("app.services.necktie_gateway")->refreshAccessToken($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $token = $user->getLastAccessToken();
        $productId = $product->getNecktieId();
        $useStoredCC = ($useStoredCC)? "useStoredCC=true" : "";

        $billingPlanIdString = null;

        if($billingPlanId) {
            foreach ($em->getRepository("VeniceAppBundle:BillingPlan")->findByProductId($product->getId()) as $billingPlan) {
                if ($billingPlanId == $billingPlan->getNecktieId()) {
                    $billingPlanIdString = "billingPlanId=$billingPlanId";
                    break;
                }
            }
        }

        //todo add expected price to the string; format of the string?

        return new RedirectResponse(
            $this->getParameter("necktie_url")."/payment/cb/ocb/{$productId}?access_token=$token&$useStoredCC&$billingPlanIdString",
            302
        );
    }
}