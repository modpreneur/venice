<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 21:08
 */

namespace Venice\AppBundle\Form\Content;


use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Form\Collection\CollectionType;
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
                "data_class" => "Venice\AppBundle\\Entity\\Content\\GroupContent",
                "groupContent" => null
            ]
        );
    }
}