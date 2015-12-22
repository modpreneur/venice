<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 13:55
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use AppBundle\Entity\Content\ContentInGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentInGroupType extends AdminBaseType
{
    /** @var  ContentInGroup */
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
                "content",
                "entity",
                [
                    "class" => "AppBundle\\Entity\\Content\\Content",
                    'query_builder' => function (EntityRepository $er)
                    {
                        return $er
                            ->createQueryBuilder('c')
                            ->andWhere('c.id != :id')
                            ->setParameter("id", $this->groupContent->getId());
                    },
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
                "integer",
                [
                    "empty_data" => 0,
                ]
            )
            ->add(
                "orderNumber",
                "integer",
                [
                    "empty_data" => 0,
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\Content\\ContentInGroup",
                "label" => " "
            ]
        );
    }
}