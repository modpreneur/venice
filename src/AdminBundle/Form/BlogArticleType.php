<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:16
 */

namespace AdminBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogArticleType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "title",
                "text",
                [
                    "required" => true
                ]
            )
            ->add(
                "handle",
                "text",
                [
                    "required" => false
                ]
            )
            ->add(
                "dateToPublish",
                "datetime",
                [
                    "required" => true,
                ]
            )

            ->add(
                "content",
                "textarea",
                [
                    "required" => true
                ]
            )
            ->add("Submit", "submit");
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\BlogArticle"
            ]
        );
    }
}