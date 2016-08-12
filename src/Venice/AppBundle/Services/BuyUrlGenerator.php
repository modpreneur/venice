<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 10:52
 */

namespace Venice\AppBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;

class BuyUrlGenerator
{
    /** @var string Necktie url */
    protected $necktieUrl;

    /** @var  RouterInterface */
    protected $router;

    /** @var  EntityManagerInterface */
    protected $entityManager;

    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, $necktieUrl)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->necktieUrl = $necktieUrl;
    }

    /**
     * Generate buy url
     *
     * @param StandardProduct $product
     * @param int $billingPlanId
     * @param bool $useStoredCreditCard
     *
     * @param string $paySystem
     * @return string
     * @throws \Exception
     */
    public function generateBuyUrl(
        StandardProduct $product,
        int $billingPlanId = null,
        bool $useStoredCreditCard = false,
        string $paySystem = ''
    ) : string {
        if ($this->necktieUrl) {
            return $this->generateNecktieBuyUrl($product, $billingPlanId, $useStoredCreditCard, $paySystem);
        } else {
            throw new \Exception('No method found to generate buy url when not connected to necktie');
        }
    }

    /**
     * @param StandardProduct $product
     * @param int $billingPlanVeniceId
     * @param bool $useStoredCreditCard
     * @param string $paySystem
     *
     * @return string
     * @throws \Exception
     */
    protected function generateNecktieBuyUrl(
        StandardProduct $product,
        int $billingPlanVeniceId = null,
        bool $useStoredCreditCard = false,
        string $paySystem = '') : string
    {
        $router = $this->router;
        $billingPlanNecktieId = null;

        $url = $router->generate(
            'necktie_buy_product',
            [
                'id' => $product->getId(),
                'paySystem' => $paySystem
            ],
            $router::ABSOLUTE_URL
        ) . '?';

        // Id not specified - use default
        if ($billingPlanVeniceId === null) {
            if ($product->getDefaultBillingPlan() === null) {
                return '';
            }
            $billingPlanNecktieId = $product->getDefaultBillingPlan()->getNecktieId();
        } elseif ($billingPlan = $this->getBillingPlan($billingPlanVeniceId)) {
            $billingPlanNecktieId = $billingPlan->getNecktieId();
        } else {
            throw new \Exception("No billing plan with venice id {$billingPlanVeniceId} found.");
        }
        $url .= "billingPlanId={$billingPlanNecktieId}";

        if ($useStoredCreditCard) {
            $url .= '&useStoredCC';
        }

        return $url;
    }

    /**
     * @param $billingPlanVeniceId
     * @return BillingPlan|null
     */
    protected function getBillingPlan($billingPlanVeniceId)
    {
        return $this->entityManager->getRepository('VeniceAppBundle:BillingPlan')->find($billingPlanVeniceId);
    }
}
