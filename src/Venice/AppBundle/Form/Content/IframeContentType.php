<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:57.
 */
namespace Venice\AppBundle\Form\Content;

use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class IframeContentType extends ContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('html', FroalaEditorType::class, ['required' => false]);
    }
}
