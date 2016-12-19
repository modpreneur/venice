<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:58.
 */
namespace Venice\AppBundle\Form\Content;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class VideoContentType.
 */
class VideoContentType extends ContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'duration',
                NumberType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'previewImage',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Preview image link',
                ]
            )
            ->add(
                'videoMobile',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'videoLq',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Video LQ',
                ]
            )
            ->add(
                'videoHq',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Video HQ',
                ]
            )
            ->add(
                'videoHd',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Video HD',
                ]
            );
    }
}
