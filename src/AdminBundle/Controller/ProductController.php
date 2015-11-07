<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:17
 */

namespace AdminBundle\Controller;


use AppBundle\Entity\Product\Product;
use AppBundle\Entity\Product\StandardProduct;
use Doctrine\DBAL\DBALException;
use ReflectionException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/admin/product")
 */
class ProductController extends BaseAdminController
{
    /**
     * @Route("", name="admin_product_index")
     * @Route("/")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getEntityManager();
        $products = $entityManager->getRepository("AppBundle:Product\\StandardProduct")->findAll();

        return $this->render(
            ":AdminBundle/Product:index.html.twig",
            [
                "products" => $products
            ]
        );
    }


    /**
     * Display a form to create a new Product entity.
     *
     * @Route("/new/{productType}",requirements={"productType": "\w+"}, name="admin_product_new")
     * @Method("GET")
     *
     * @param Request $request
     * @param         $productType
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param $type
     *
     */
    public function newAction(Request $request, $productType)
    {
        try
        {
            $product = Product::createProductByType($productType);
        }
        catch(ReflectionException $e)
        {
            throw new NotFoundHttpException("Product type: " . $productType . " not found.");
        }

        $form = $this->createForm(
            $product->getFormType([$product, $this->getCMSProblemHelper()]),
            $product,
            [
                'action' => $this->generateUrl(
                    'admin_product_create',
                    [
                        "productType" => $productType
                    ]
                ),
            ]
        );

        return $this->render(
            ':AdminBundle/Product:new.html.twig',
            [
                'entity'     => $product,
                'form'       => $form->createView()
            ]
        );
    }


    /**
     * Process a request to create a new Product entity.
     *
     * @Route("/create/{productType}",requirements={"productType": "\w+"}, name="admin_product_create")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @param string  $productType
     *
     * @return JsonResponse
     */
    public function createAction(Request $request, $productType)
    {
        try
        {
            $product = Product::createProductByType($productType);
        }
        catch(ReflectionException $e)
        {
            throw new NotFoundHttpException("Product type: " . $productType . " not found.");
        }

        $em = $this->getEntityManager();

        $productForm = $this->createForm($product->getFormType([$product, $this->getCMSProblemHelper()]), $product);
        $productForm->handleRequest($request);

        if($productForm->isValid())
        {
            $billingPlanId = $productForm->get("billingPlanId")->getData();

            if($product instanceof StandardProduct)
            {
                $billingPlan = $this->get("app.services.connection_manager")->getBillingPlan($this->getUser(), $billingPlanId);

                if(!$billingPlan)
                {
                    return new JsonResponse(["errors" => ["No billing plan found"]]);
                }

                $product->setBillingPlan($billingPlan);
                $em->persist($billingPlan);
            }

            $em->persist($product);

            try
            {
                $em->flush();
            }
            catch (DBALException $e)
            {
                return new JsonResponse(['errors' => ['db' => $e->getMessage()]]);
            }

            return new JsonResponse(["message" => "Product successfully created"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }


    /**
     * Display a form to edit a Product entity.
     *
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="admin_product_edit")
     * @Method("GET")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Product $product)
    {
        $productType = $product->getFormType([$product, $this->getCMSProblemHelper()]);
        $productForm = $this->createForm(
            $productType,
            $product,
            [
                "action" => $this->generateUrl(
                    "admin_product_update",
                    [
                        "id" => $product->getId()
                    ]
                )
            ]
        );

        return $this->render(
            ":AdminBundle/Product:edit.html.twig",
            [
                "entity" => $product,
                "form" => $productForm->createView()
            ]

        );
    }


    /**
     * Process a request to update a Product entity.
     *
     * @Route("/{id}/update", requirements={"id": "\d+"}, name="admin_product_update")
     * @Method("POST")
     *
     * @param Request $request
     * @param Product $product
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, Product $product)
    {
        $productType = $product->getFormType([$product, $this->getCMSProblemHelper()]);
        $productForm = $this->createForm($productType, $product);
        $em = $this->getEntityManager();

        $productForm->handleRequest($request);

        if($productForm->isValid())
        {
            //if the updated product is StandardProduct edit it's billing plan
            if($product instanceof StandardProduct)
            {
                $billingPlanId = $productForm->get("billingPlanId")->getData();
                $newBillingPlan = $this->get("app.services.connection_manager")->getBillingPlan($this->getUser(), $billingPlanId);

                if(!$newBillingPlan)
                {
                    return new JsonResponse(["errors" => ["No billing plan found"]]);
                }

                //remove odl billing plan and persist a new one
                $oldBillingPlan = $product->getBillingPlan();
                $em->remove($oldBillingPlan);
                $product->setBillingPlan(null);
                $em->flush();
                $product->setBillingPlan($newBillingPlan);
                $em->persist($newBillingPlan);
            }

            $em->persist($product);

            try
            {
                $em->flush();
            }
            catch (DBALException $e)
            {
                return new JsonResponse(["errors" => ["db" => $e->getMessage()]]);
            }

            return new JsonResponse(["message" => "Product successfully updated"]);
        }
        else
        {
            return $this->returnFormErrorsJsonResponse($productForm);
        }
    }
}