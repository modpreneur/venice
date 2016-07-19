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
use Venice\AppBundle\Entity\Product\StandardProduct;

/**
 * Class ThankYouController
 * @package Venice\AppBundle\Controller
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
        //@todo @JakubFajkus
        $productId = $request->get('productId');
        $product = null;
        if ($productId !== null) {
            $product = $this->getDoctrine()->getRepository(StandardProduct::class)
                ->findOneBy(['necktieId' => $productId]);
        }

        return $this->redirectToRoute(
            'front_thank_you',
            ['productId' => ($product !== null)? $product->getId() : null]
        );
    }
}
