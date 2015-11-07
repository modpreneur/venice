<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:58
 */

namespace AdminBundle\Form\Content;


use Symfony\Component\Form\FormBuilderInterface;

class VideoContentType extends ContentType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("length", "text", [
                    "required" => true
                ]
            )
            ->add("previewImage", "text", [
                    "required" => true,
                    "label" => "Preview image link"
                ]
            )
            ->add("videoMobile", "text", [
                    "required" => false
                ]
            )
            ->add("videoLq", "text", [
                    "required" => false
                ]
            )
            ->add("videoHq", "text", [
                    "required" => false
                ]
            )
            ->add("videoHd", "text", [
                    "required" => false
                ]
            )
        ;
    }

}