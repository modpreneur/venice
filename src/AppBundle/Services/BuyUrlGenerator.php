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

    public function __construct($necktieUrl, RouterInterface $router)
    {
        $this->necktieUrl = $necktieUrl;
        $this->router = $router;
    }

    /**
     * Generate buy url
     *
     * @param StandardProduct $product
     * @param $useStoredCreditCard
     * @return string
     * @throws \Exception
     */
    public function generateBuyUrl(StandardProduct $product, $useStoredCreditCard = false)
    {
        $router = $this->router;

        // Connected to necktie
        if ($this->necktieUrl) {
            $url = $router->generate(
                "necktie_buy_product",
                [
                    "id" => $product->getId(),
                ],
                $router::ABSOLUTE_URL
            );

            if ($useStoredCreditCard) {
                $url .= ($useStoredCreditCard)? "?useStoredCC" : "";
            }

            return $url;

        } else {
            throw new \Exception("Necktie url is not defined!");
        }
    }
}