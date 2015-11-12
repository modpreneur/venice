<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:54
 */

namespace AdminBundle\Form\Content;


use Symfony\Component\Form\FormBuilderInterface;

class IframeContentType extends ContentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("html", "textarea")
            ->add("Submit", "submit");
    }

}