<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:55.
 */

namespace Venice\AppBundle\Form\Content;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class Mp3ContentType.
 */
class Mp3ContentType extends ContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'link',
                TextType::class
            )
            ->add(
                'duration',
                NumberType::class,
                [
                    'label' => 'Duration in seconds',
                ]
            );
    }
}
