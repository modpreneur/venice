<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 01.02.16
 * Time: 17:56
 */

namespace AppBundle\Form\ContentProduct;


use AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentProductTypeWithHiddenContent extends ContentProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "product",
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\Product\\Product",
                    "choice_label" => "name"
                ]
            )
            ->add(
                "content",
                HiddenType::class,
                [
                      // Uses model transformer
                    "data" => $options["content"],
                    "data_class" => null,
                    "label" => false
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
            ->get("content")
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "AppBundle:Content\\Content"
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault("content", null);
    }

}