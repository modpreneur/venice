<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:54.
 */

namespace Venice\AppBundle\Form\Content;

use Trinity\AdminBundle\Form\FroalaType\FroalaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * {@inheritdoc}
 */
class HtmlContentType extends ContentType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('html', FroalaType::class, [
                'required' => false,
                'attr' => [
                'style' => 'display:none',
                ],
            ]);
    }
}
