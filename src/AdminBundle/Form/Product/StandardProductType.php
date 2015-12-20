<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:28
 */

namespace AdminBundle\Form\Product;


use AppBundle\Entity\Product\StandardProduct;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class StandardProductType extends ProductType
{
    function __construct(StandardProduct $product = null)
    {
        parent::__construct($product);

        $this->product = $product;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            //todo: how to work with billing plans?
//            ->add(
//                "billingPlan",
//                "entity",
//                [
//                    "class" => "AppBundle\\Entity\\BillingPlan",
//                    "choice_label" => "price",
//                    "multiple" => "false",
//                    "required" => "true",
//                    "empty_data" => "no billing plan"
//                ]
//            )
            ->add(
                "submit",
                "submit",
                [
                    "label" => "Create"
                ]
            )
        ;
    }

}