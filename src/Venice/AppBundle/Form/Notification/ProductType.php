<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.04.16
 * Time: 12:04.
 */

namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Form\BaseType;

/**
 * {@inheritdoc}
 */
class ProductType extends BaseType implements NotificationTypeInterface
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
                'productType',
                TextType::class
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'property_path' => 'necktieDescription'
                ]
            )
            ->add(
                'id',
                IntegerType::class,
                [
                    'property_path' => 'necktieId'
                ]
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
                'data_class' => StandardProduct::class,
                'csrf_protection' => false,
            ]
        );
    }
}
