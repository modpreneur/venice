<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.07.16
 * Time: 15:53
 */

namespace Venice\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class ThankYouController
 * @package Venice\FrontBundle\Controller
 */
class ThankYouController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function thankYouAction(Request $request)
    {
        $productId = $request->get('productId');
        $product = null;

        if ($productId !== null) {
            $product = $this->getDoctrine()->getRepository(StandardProduct::class)->find($productId);
        }

        return $this->render('VeniceFrontBundle:ThankYou:thankYou.html.twig', ['product' => $product]);
    }
}
