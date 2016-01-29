<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.11.15
 * Time: 16:33
 */

namespace AppBundle\Form\Content;


use AppBundle\Form\BaseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentProductType extends BaseType
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
                IntegerType::class,
                [
                    "required" => true,
                    "empty_data" => 0

                ]
            )
            ->add(
                "orderNumber",
                IntegerType::class,
                [
                    "required" => true,
                    "empty_data" => 0
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