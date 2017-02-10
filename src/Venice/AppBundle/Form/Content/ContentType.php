<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:48.
 */

namespace Venice\AppBundle\Form\Content;

use Venice\AppBundle\Form\BaseType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContentType.
 */
abstract class ContentType extends BaseType
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
                TextType::class
            )
            ->add(
                'description',
                TextType::class,
                [
                    'required' => false
                ]
            );
    }
}
