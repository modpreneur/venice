<?php

namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
 * Class PaySystemType.
 */
class PaySystemType extends BaseType implements NotificationTypeInterface
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
                'defaultVendor',
                HiddenType::class,
                [
                ]
            )
            ->add(
                'id',
                IntegerType::class,
                ['property_path' => 'necktieId']
            );

        $builder->get('defaultVendor')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    PaySystemVendor::class,
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
                'data_class' => PaySystem::class,
                'csrf_protection' => false,
            ]
        );
    }
}
