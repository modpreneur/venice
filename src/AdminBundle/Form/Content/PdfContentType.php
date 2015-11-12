<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:55
 */

namespace AdminBundle\Form\Content;


use Symfony\Component\Form\FormBuilderInterface;

class PdfContentType extends ContentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("link", "text")
            ->add("Submit", "submit");
    }

}