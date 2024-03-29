<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.04.16
 * Time: 12:43.
 */

namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\DataTransformer\NotificationTransformer;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\PaySystemVendor;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\BaseType;

/**
 * Class BillingPlanType.
 */
class BillingPlanType extends BaseType implements NotificationTypeInterface
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
                TextType::class
            )
            ->add(
                'itemId',
                TextType::class
            )->add(
                'paySystemVendor',
                TextType::class
            )
            ->add(
                'id',
                IntegerType::class,
                [
                    'property_path' => 'necktieId'
                ]
            );

        $builder->get('product')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    $options['standardProductClass'],
                    'necktieId'
                )
            );

        $builder->get('paySystemVendor')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    $options['paySystemVendorClass'],
                    'necktieId'
                )
            );
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'data_class' => BillingPlan::class,
                'standardProductClass' => StandardProduct::class,
                'paySystemVendorClass' => PaySystemVendor::class,
            ]
        );
    }
}
