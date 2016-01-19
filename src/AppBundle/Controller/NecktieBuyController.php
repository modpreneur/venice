<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 12:03
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/buy")
 *
 * Class NecktieBuyController
 */
class NecktieBuyController extends Controller
{
    /**
     * @Route("/product/{id}", name="necktie_buy_product")
     *
     * @param Request $request
     * @param StandardProduct $product
     *
     * @return RedirectResponse
     *
     * @internal param bool $useStoredCC Use stored credit card
     *
     */
    public function redirectToNecktieBuy(Request $request, StandardProduct $product)
    {
        $useStoredCC = $request->query->has("useStoredCC");

        /** @var User $user */
        $user = $this->getUser();

        $this->get("app.services.necktie_gateway")->refreshAccessToken($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $token = $user->getLastAccessToken();
        $productId = $product->getNecktieId();

        return new RedirectResponse(
            $this->getParameter("necktie_url")."/payment/cb/ocb/{$productId}?access_token=$token&".(($useStoredCC) ? "useStoredCC=true" : ""),
            302
        );
    }
}