<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:03
 */

namespace AdminBundle\Form\Product;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", TextType::class)
            ->add("description", TextareaType::class, ["required" => false])
            ->add("image", TextType::class)
            ->add("enabled", CheckboxType::class)
            ->add("orderNumber", IntegerType::class)
        ;
    }
}