<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 10:18.
 */

namespace Venice\AppBundle\Form\Product;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FreeProductType.
 */
class FreeProductType extends ProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class
            );

        parent::buildForm($builder, $options);

        $builder
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Create'
                ]
            );
    }
}
