<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.01.16
 * Time: 15:43
 */

namespace FrontBundle\Controller;


use AppBundle\Entity\Product\Product;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/front/product")
 *
 * Class ProductController
 */
class ProductController extends Controller
{
    /**
     * @Route("", name="front_product_index")
     * @Route("/")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getRepository("AppBundle:Product\\Product")->findAll();

        return $this->render(":FrontBundle/Product:index.html.twig", ["products" => $products]);
    }

    /**
     * @Route("/show/{id}", name="front_product_show")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Product $product)
    {
        return $this->render(":FrontBundle/Product:show.html.twig", ["product" => $product]);
    }

    /**
     * @Route("/demo/{id}", name="front_product_demo")
     *
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function demoAction(Product $product)
    {
        return $this->render(":FrontBundle/Product:demo.html.twig",["product" => $product]);
    }

}