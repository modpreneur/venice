<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 12:03.
 */
namespace Venice\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\Interfaces\UserInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class NecktieBuyController.
 */
class NecktieBuyController extends Controller
{
    /**
     * @param Request $request
     * @param StandardProduct $product
     * @param string $paySystem
     *
     * @return RedirectResponse
     *
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \LogicException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function redirectToNecktieBuy(Request $request, StandardProduct $product, string $paySystem)
    {
        try {
            return $this->createRedirectBuyResponse($request, $product, $paySystem);
        } catch (\Throwable $error) {
            $this->logError($product, $paySystem, $error);

            throw $error;
        }
    }

    /**
     * @param StandardProduct $product
     * @param string $paySystem
     */
    protected function logError(StandardProduct $product, string $paySystem, \Throwable $error)
    {
        $this->get('logger')->emergency(
            "Could no buy a product with necktieId: {$product->getNecktieId()} with pay system {$paySystem}. ' . 
            'Original error: ".$error->getTraceAsString()
        );
    }

    /**
     * @param Request $request
     * @param StandardProduct $product
     * @param string $paySystem
     *
     * @return RedirectResponse
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentNotImplementedException
     * @throws \Trinity\Bundle\SettingsBundle\Exception\PropertyNotExistsException
     * @throws \Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @throws \LogicException
     */
    protected function createRedirectBuyResponse(Request $request, StandardProduct $product, string $paySystem)
    {
        $priceStringGenerator = $this->get('trinity.services.price_string_generator');

        if (!$product->isPurchasable()) {
            throw new \LogicException('Can not buy product '.$product->getName().' as it is not purchasable.');
        }

        /** @var UserInterface $user */
        $user = $this->getUser();
        $this->get('venice.app.necktie_gateway')->refreshAccessToken($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $token = $user->getLastAccessToken();
        $useStoredCC = $request->query->has('useStoredCC') ? 'useStoredCC=true' : '';
        $billingPlanId = $request->query->get('billingPlanId');
        $productId = $product->getNecktieId();

        if ($paySystem === 'ClickBank') {
            $paySystemUrlPart = 'click-bank/ocb';
        } elseif ($paySystem === 'Braintree') {
            $paySystemUrlPart = 'braintree/buy';
        } else {
            $this->get('logger')->emergency("Can not buy product");
            throw new NotFoundHttpException('Unsupported pay system: '.$paySystem);
        }

        if ($billingPlanId) {
            $billingPlan = $entityManager->getRepository('VeniceAppBundle:BillingPlan')->findOneBy(
                ['necktieId' => $billingPlanId]
            );

            if (!$billingPlan) {
                throw new NotFoundHttpException('No billing plan found with id: ' . $billingPlanId);
            }

            $price = $priceStringGenerator->generateFullPriceStr($billingPlan);

            return new RedirectResponse(
                $this->getParameter('necktie_url')."/payment/{$paySystemUrlPart}/billing/{$billingPlanId}?access_token={$token}&{$useStoredCC}&price={$price}",
                302
            );
        } else {
            $price = $priceStringGenerator->generateFullPriceStr($product->getDefaultBillingPlan());

            return new RedirectResponse(
                $this->getParameter('necktie_url')."/payment/{$paySystemUrlPart}/{$productId}?access_token={$token}&{$useStoredCC}&price={$price}",
                302
            );
        }
    }
}
