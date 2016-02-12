<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:28
 */

namespace AppBundle\Form\Product;


use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardProductType extends ProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("necktieId", NumberType::class, ["required" => true])
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