<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:28
 */

namespace AdminBundle\Form\Product;


use AppBundle\Entity\Product\StandardProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add(
                "billingPlan",
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\BillingPlan",
                    "choice_label" => "price",
                    "required" => "true",
                    "empty_data" => "No billing plans found"
                ]
            )
            ->add(
                "submit",
                SubmitType::class,
                [
                    "label" => "Create"
                ]
            )
        ;
    }

}