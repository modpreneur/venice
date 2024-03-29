<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:03.
 */

namespace Venice\AppBundle\Form\Product;

use Venice\AppBundle\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProductType
 */
class ProductType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'handle',
                TextType::class
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Venice description'
                ]
            )
            ->add(
                'image',
                TextType::class
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => [
                        'disable_widget_label' => true
                    ],
                ]
            )
            ->add(
                'orderNumber',
                IntegerType::class
            );
    }
}
