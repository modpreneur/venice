<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 21:08
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\Collection\CollectionType;
use AppBundle\Entity\Content\GroupContent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupContentType extends ContentType
{
    /** @var  GroupContent */
    protected $groupContent;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "handle",
                TextType::class,
                [
                    "required" => false,
                ]
            )
            ->add(
                "items",
                CollectionType::class,
                [
                    "type" => ContentInGroupType::class,
                    "options" => ["groupContent" => $options["groupContent"]],
                    "required" => false,
                    "label" => "Contents",
                    "allow_add" => true,
                    "allow_delete" => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\Content\\GroupContent",
                "groupContent" => null
            ]
        );
    }
}