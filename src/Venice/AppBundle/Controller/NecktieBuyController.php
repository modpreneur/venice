<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 12:03
 */

namespace Venice\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\User;

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
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \LogicException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \InvalidArgumentException
     */
    public function redirectToNecktieBuy(Request $request, StandardProduct $product, string $paySystem)
    {
        $priceStringGenerator = $this->get('trinity.services.price_string_generator');

        /** @var User $user */
        $user = $this->getUser();
        $this->get('venice.app.necktie_gateway')->refreshAccessToken($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $token = $user->getLastAccessToken();
        $useStoredCC = $request->query->has('useStoredCC') ? 'useStoredCC=true' : '';
        $billingPlanId = $request->query->get('billingPlanId');
        $productId = $product->getNecktieId();

        if ($paySystem === 'cb') {
            $paySystemUrlPart = 'cb/ocb';
        } elseif ($paySystem === 'braintree') {
            $paySystemUrlPart = 'braintree/buy';
        } else {
            throw new NotFoundHttpException('Unsupported pay system: ' . $paySystem);
        }

        if ($billingPlanId) {
            $billingPlan = $entityManager->getRepository('VeniceAppBundle:BillingPlan')->findOneBy(['necktieId' => $billingPlanId]);
            if (!$billingPlan) {
                throw new NotFoundHttpException('No billing plan found');
            }

            $price = $priceStringGenerator->generateFullPriceStr($billingPlan);

            return new RedirectResponse(
                $this->getParameter('necktie_url') . "/payment/{$paySystemUrlPart}/billing/{$billingPlanId}?access_token={$token}&{$useStoredCC}&price={$price}",
                302
            );
        } else {
            $price = $priceStringGenerator->generateFullPriceStr($product->getDefaultBillingPlan());

            return new RedirectResponse(
                $this->getParameter('necktie_url') . "/payment/{$paySystemUrlPart}/{$productId}?access_token={$token}&{$useStoredCC}&price={$price}",
                302
            );
        }
    }
}
