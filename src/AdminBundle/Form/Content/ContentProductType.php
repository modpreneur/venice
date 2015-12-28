<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.11.15
 * Time: 16:33
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\Content\\Content",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "product",
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\Product\\Product",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "delay",
                NumberType::class,
                [
                    "required" => true,
                    "data" => 0

                ]
            )
            ->add(
                "orderNumber",
                NumberType::class,
                [
                    "required" => true,
                    "data" => 0
                ]
            );

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