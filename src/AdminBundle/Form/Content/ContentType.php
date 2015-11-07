<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:48
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("name", "text");
    }

}