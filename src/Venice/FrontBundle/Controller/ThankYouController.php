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
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function thankYouAction(Request $request)
    {
        $productId = $request->get('productId');
        $product = null;

        if($productId !== null)
        $product = $this->getDoctrine()->getRepository('VeniceAppBundle:Product\StandardProduct')
            ->find($productId);

        return $this->render('VeniceFrontBundle:ThankYou:thankYou.html.twig', ['product' => $product]);
    }
}