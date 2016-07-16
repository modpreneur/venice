<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.07.16
 * Time: 15:53
 */

namespace Venice\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class ThankYouController
 * @package Venice\AppBundle\Controller
 */
class ThankYouController extends Controller
{
    /**
     * @param Request $request
     * @param         $necktieProductId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function thankYouAction(Request $request, $necktieProductId)
    {
        $product = $this->getDoctrine()->getRepository('VeniceAppBundle:Product\StandardProduct')
            ->findOneBy(['necktieId' => $necktieProductId]);

        if (!$product) {
            throw new NotFoundHttpException("No Product with id {$necktieProductId} found.");
        }

        return $this->redirectToRoute('front_thank_you', ['productId' => $product->getId()]);
    }
}