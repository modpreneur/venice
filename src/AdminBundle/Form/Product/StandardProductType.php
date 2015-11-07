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
    protected $product;
    protected $CMSProblemHelper;

    function __construct(StandardProduct $product = null, CMSProblemHelper $CMSProblemHelper)
    {
        $this->product = $product;
        $this->CMSProblemHelper = $CMSProblemHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $billingPlanId = null;

        if($this->product && $this->product->getBillingPlan())
        {
            $billingPlan = $this->product->getBillingPlan();
            $billingPlanId = $this->CMSProblemHelper->getBillingPlanId($billingPlan);
            //ldd($billingPlanId);
        }
        else
        {
           //ldd($this->product,$this->product->getBillingPlan());
        }

        $builder
            ->add("billingPlanId", "integer", ["mapped" => false, "data" => $billingPlanId])
            ->add("Submit", "submit")
        ;
    }

}