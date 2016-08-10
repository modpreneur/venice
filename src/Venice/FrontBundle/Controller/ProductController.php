<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.01.16
 * Time: 15:43
 */

namespace Venice\FrontBundle\Controller;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\User;

/**
 * Class ProductController
 */
class ProductController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getRepository("VeniceAppBundle:Product\\Product")->findAll();
        $urlGenerator = $this->get('venice.app.buy_url_generator');

        return $this->render(
            'VeniceFrontBundle:Product:index.html.twig',
            [
                'products' => $products,
                'urlGenerator' => $urlGenerator
            ]
        );
    }

    /**
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Product $product)
    {
        return $this->render('VeniceFrontBundle:Product:show.html.twig',
            ['product' => $product, 'urlGenerator' => $this->get('venice.app.buy_url_generator')]
        );
    }

    /**
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function demoAction(Product $product)
    {
        return $this->render('VeniceFrontBundle:Product:demo.html.twig', ['product' => $product]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersionIndexAction()
    {
        return $this->render('VeniceFrontBundle:Product:immersionIndex.html.twig');
    }

    /**
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersionAction(Product $product)
    {
        $immersion = $product->getAllContentProductsForImmersion();

        return $this->render('VeniceFrontBundle:Product:immersion.html.twig', ['immersion' => $immersion]);
    }

    /**
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersion2Action(Product $product)
    {
        $immersion = $product->getAllContentProductsForImmersion();

        return $this->render(
            'FrontBundle:Product:immersion2.html.twig',
            [
                'immersion' => $immersion,
                'product' => $product,
            ]
        );
    }
}