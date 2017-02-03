<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.04.16
 * Time: 12:04.
 */

namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\DataTransformer\NotificationTransformer;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Form\BaseType;

/**
 * Class ProductAccessType.
 */
class ProductAccessType extends BaseType implements NotificationTypeInterface
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
                'product',
                TextType::class
            )
            ->add(
                'fromDate',
                DateTimeType::class,
                [
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'toDate',
                DateTimeType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'user',
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

        $builder->get('user')
            ->addModelTransformer(
                new NotificationTransformer(
                    $this->entityManager,
                    $options['userClass'],
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
                'data_class' => ProductAccess::class,
                'standardProductClass' => StandardProduct::class,
                'userClass' => User::class,
            ]
        );
    }
}
