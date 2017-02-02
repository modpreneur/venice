<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 21.01.16
 * Time: 14:29.
 */
namespace Venice\AppBundle\Form;

use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillingPlanType
 *
 * This class is not used anymore
 */
class BillingPlanType extends BaseType
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\Form\Exception\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'initialPrice',
                IntegerType::class
            )
            ->add(
                'rebillPrice',
                IntegerType::class
            )
            ->add(
                'frequency',
                IntegerType::class
            )
            ->add(
                'rebillTimes',
                IntegerType::class
            )
            ->add(
                'product',
                HiddenType::class,
                [
                    // Uses model transformer
                    'data' => $options['product'],
                    'data_class' => null,
                    'label' => false,
                ]
            );

        $builder->get('product')
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    StandardProduct::class
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => BillingPlan::class,
                'product' => null,
            ]
        );
    }
}
