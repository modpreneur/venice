<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:03
 */

namespace AdminBundle\Form\Product;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", "text")
            ->add("description", "textarea", ["required" => false])
            ->add("image", "text")
            ->add("enabled", "checkbox")
            ->add("orderNumber", "integer")
        ;
    }
}