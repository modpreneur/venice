<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 01.02.16
 * Time: 17:57
 */

namespace AppBundle\Form\ContentProduct;


use AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentProductTypeWithHiddenProduct extends ContentProductType
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
                HiddenType::class,
                [
                    // Uses model transformer
                    "data" => $options["product"],
                    "data_class" => null,
                    "label" => null,
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


        $builder
            ->get("product")
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "AppBundle:Product\\Product"
                )
            );

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault("product", null);
    }


}