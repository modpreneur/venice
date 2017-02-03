<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:28.
 */

namespace Venice\AppBundle\Form\Product;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class StandardProductType.
 */
class StandardProductType extends ProductType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'disabled' => true
                ]
            )
            ->add(
                'necktieId',
                NumberType::class,
                [
                    'required' => true
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Create',
                ]
            );
    }
}
