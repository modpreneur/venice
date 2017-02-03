<?php

namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\DataTransformer\NotificationTransformer;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\PaySystem;
use Venice\AppBundle\Entity\PaySystemVendor;
use Venice\AppBundle\Form\BaseType;

/**
 * Class PaySystemVendor.
 */
class PaySystemVendorType extends BaseType implements NotificationTypeInterface
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
                'name',
                TextType::class
            )
            ->add(
                'paySystem',
                TextType::class
            )
            ->add(
                'id',
                IntegerType::class,
                [
                    'property_path' => 'necktieId'
                ]
            );

        $builder->get('paySystem')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    PaySystem::class,
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
                'data_class' => PaySystemVendor::class,
            ]
        );
    }
}
