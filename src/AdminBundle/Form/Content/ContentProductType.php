<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.11.15
 * Time: 16:33
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentProductType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "content",
                "entity",
                [
                    "class" => "AppBundle\\Entity\\Content\\Content",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "product",
                "entity",
                [
                    "class" => "AppBundle\\Entity\\Product\\Product",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "delay",
                "number",
                [
                    "required" => true,
                    "data" => 0

                ]
            )
            ->add(
                "orderNumber",
                "number",
                [
                    "required" => true,
                    "data" => 0
                ]
            )
            ->add(
                "Submit",
                "submit"
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\ContentProduct"
            ]
        );
    }
}