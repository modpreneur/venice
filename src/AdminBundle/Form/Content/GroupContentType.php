<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 21:08
 */

namespace AdminBundle\Form\Content;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupContentType extends ContentType
{
    protected $groupContent;


    public function __construct($groupContent)
    {
        $this->groupContent = $groupContent;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "items",
                "collection",
                [
                    "type" => new ContentInGroupType($this->groupContent),
                    "label" => "Collection?",
                    "allow_add" => true,
                    'allow_delete' => true

                ]

            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\Content\\GroupContent"
            ]
        );
    }
}