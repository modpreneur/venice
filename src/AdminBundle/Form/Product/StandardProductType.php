<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:28
 */

namespace AdminBundle\Form\Product;


use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Services\CMSProblemHelper;
use Symfony\Component\Form\FormBuilderInterface;

class StandardProductType extends ProductType
{
    function __construct(StandardProduct $product = null, CMSProblemHelper $CMSProblemHelper)
    {
        parent::__construct($product, $CMSProblemHelper);

        $this->product = $product;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $billingPlanId = null;

        if($this->product && $this->product->getBillingPlan())
        {
            $billingPlan = $this->product->getBillingPlan();
            $billingPlanId = $this->CMSProblemHelper->getBillingPlanId($billingPlan);
        }

        $builder
            ->add("billingPlanId", "integer", ["mapped" => false, "data" => $billingPlanId])
            ->add("Submit", "submit")
        ;
    }

}