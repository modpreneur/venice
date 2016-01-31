<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 10:52
 */

namespace AppBundle\Services;


use AppBundle\Entity\Product\StandardProduct;
use Symfony\Component\Routing\RouterInterface;

class BuyUrlGenerator
{
    /** @var string Necktie url */
    protected $necktieUrl;

    /** @var  RouterInterface */
    protected $router;

    public function __construct(RouterInterface $router, $necktieUrl)
    {
        $this->router = $router;
        $this->necktieUrl = $necktieUrl;
    }

    /**
     * Generate buy url
     *
     * @param StandardProduct $product
     * @param $useStoredCreditCard
     * @return string
     * @throws \Exception
     */
    public function generateBuyUrl(StandardProduct $product, bool $useStoredCreditCard = false) : string
    {
        if ($this->necktieUrl) {
            return $this->generateNecktieBuyUrl($product, $useStoredCreditCard);
        }
        else {
            throw new \Exception("No method found to generate buy url when not connected to necktie");
        }
    }


    /**
     * @param StandardProduct $product
     * @param bool $useStoredCreditCard
     * @return string
     */
    protected function generateNecktieBuyUrl(StandardProduct $product, bool $useStoredCreditCard = false) : string
    {
        $router = $this->router;

        $url = $router->generate(
            "necktie_buy_product",
            [
                "id" => $product->getId(),
            ],
            $router::ABSOLUTE_URL
        );

        if ($useStoredCreditCard) {
            $url .= ($useStoredCreditCard) ? "?useStoredCC" : "";
        }

        return $url;
    }
}