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
        $urlGenerator = $this->get("app.services.buy_url_generator");

        return $this->render(
            "FrontBundle:Product:index.html.twig",
            [
                "products" => $products,
                "urlGenerator" => $urlGenerator
            ]
        );
    }

    /**
     * @Route("/show/{id}", name="front_product_show")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Product $product)
    {
        return $this->render("FrontBundle:Product:show.html.twig", ["product" => $product]);
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
        return $this->render("FrontBundle:Product:demo.html.twig", ["product" => $product]);
    }


    /**
     * @Route("/immersionIndex", name="front_product_immersion_index")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersionIndexAction()
    {
        return $this->render("FrontBundle:Product:immersionIndex.html.twig");
    }


    /**
     * @Route("/immersion/{id}", name="front_product_immersion")
     *
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersionAction(Product $product)
    {
        $immersion = $product->getAllContentProductsForImmersion();

        return $this->render("FrontBundle:Product:immersion.html.twig", ["immersion" => $immersion]);
    }


    /**
     * @Route("/immersion2/{id}", name="front_product_immersion2")
     *
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function immersion2Action(Product $product)
    {
        $immersion = $product->getAllContentProductsForImmersion();

        return $this->render(
            "FrontBundle:Product:immersion2.html.twig",
            [
                "immersion" => $immersion,
                "product" => $product,
            ]
        );
    }

}