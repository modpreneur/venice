<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:57
 */

namespace AdminBundle\Form\Content;


use Symfony\Component\Form\FormBuilderInterface;

class TextContentType extends ContentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("text", "text")
            ->add("Submit", "submit")
        ;
    }

}