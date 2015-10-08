<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product\Product;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    //    $product = new Product();
    //    $product->setName("Test product");
    //    $product->setDescription("Testing description");
    //    $product->setEnabled(true);
    //    $product->setOrderNumber(1);
    //    $product->createHandle($product->getName());
    //    $product->setImage("image.jpg");

        /** @var EntityManager $entityManager */
        //$entityManager = $this->getDoctrine()->getManager();
        //$entityManager->persist($product);
        //$entityManager->flush();

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }
}
