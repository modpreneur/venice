<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.01.16
 * Time: 14:29
 */

namespace AppBundle\Form;


use AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingPlanType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "amemberId",
                IntegerType::class
            );

        $builder
            ->add(
                "initialPrice",
                IntegerType::class
            )
            ->add(
                "rebillPrice",
                IntegerType::class
            )
            ->add(
                "frequency",
                IntegerType::class
            )
            ->add(
                "rebillTimes",
                IntegerType::class
            )
            ->add(
                "product",
                HiddenType::class,
                [
                    // Uses model transformer
                    "data" => $options["product"],
                    "data_class" => null,
                    "label" => false,
                ]
            );

        $builder->get("product")
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "AppBundle:Product\\StandardProduct"
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\BillingPlan",
                "product" => null,
            ]
        );
    }

}