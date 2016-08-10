<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.04.16
 * Time: 12:43
 */

namespace Venice\AppBundle\Form\Notification;


use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\DataTransformer\NotificationTransformer;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\BaseType;


/**
 * Class BillingPlanType
 */
class BillingPlanType extends BaseType implements NotificationTypeInterface
{
    /**
     * {@inheritdoc}
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
                TextType::class,
                [
                ]
            )
            ->add(
                'id',
                IntegerType::class,
                ['property_path' => 'necktieId']
            );
        
        $builder->get('product')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    $options['standardProductClass'],
                    'necktieId'
                )
            );
    }


    /**
     * {@inheritdoc}
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'data_class' => BillingPlan::class,
                'csrf_protection' => false,
                'standardProductClass' => StandardProduct::class
            ]
        );
    }


    /**
     * Will be called after the
     *
     * @return mixed
     */
    public function onSuccess()
    {
        // TODO: Implement onSuccess() method.
    }


    /**
     * @return mixed
     */
    public function onFailure()
    {
        // TODO: Implement onFailure() method.
    }
}
