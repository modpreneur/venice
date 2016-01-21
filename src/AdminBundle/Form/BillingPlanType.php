<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.01.16
 * Time: 14:29
 */

namespace AdminBundle\Form;


use AdminBundle\Form\DataTransformer\EntityToNumberTransformer;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillingPlanType extends BaseType
{
    /** @var StandardProduct */
    protected $product;

    protected $entityManager;

    public function __construct(StandardProduct $product, $entityManager)
    {
        $this->product = $product;
        $this->entityManager = $entityManager;
    }


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
                    "data" => $this->product,
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
            ]
        );
    }

}