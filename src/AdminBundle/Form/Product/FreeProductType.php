<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 10:18
 */

namespace AdminBundle\Form\Product;


use Symfony\Component\Form\FormBuilderInterface;

class FreeProductType extends ProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("Submit", "submit");
    }

}