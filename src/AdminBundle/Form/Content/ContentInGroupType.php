<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 13:55
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentInGroupType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "content",
                "entity",
                [
                    "class" => "AppBundle\\Entity\\Content\\Content",
                    "choice_label" => "name",
                    "label" => "Content"
                ]
            )
            ->add(
                "group",
                "entity",
                [
                    "class" => "AppBundle\\Entity\\Content\\GroupContent",
                    "choice_label" => "name",
                    "label" => "Group",
                    "attr" => ["class" => "hidden"] //todo: remove?
                ]
            )
            ->add(
                "delay",
                "number"
            )
            ->add(
                "orderNumber",
                "number"
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\Content\\ContentInGroup"
            ]
        );
    }
}