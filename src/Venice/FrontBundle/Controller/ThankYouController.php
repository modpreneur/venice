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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class ThankYouController
 * @package Venice\FrontBundle\Controller
 */
class ThankYouController extends Controller
{
    /**
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $necktieProductId
     *
     */
    public function thankYouAction(Request $request, $productId)
    {
        $product = $this->getDoctrine()->getRepository('VeniceAppBundle:Product\StandardProduct')
            ->find($productId);

        return $this->render('VeniceFrontBundle:ThankYou:thankYou.html.twig', ['product' => $product]);
    }
}