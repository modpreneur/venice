<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 21:08
 */

namespace AdminBundle\Form\Content;


use AppBundle\Entity\Content\GroupContent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupContentType extends ContentType
{
    /** @var  GroupContent */
    protected $groupContent;

    /** @var  EntityManagerInterface */
    protected $entityManager;

    /**
     * GroupContentType constructor.
     * @param GroupContent $groupContent
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(GroupContent $groupContent, EntityManagerInterface $entityManager)
    {
        $this->groupContent = $groupContent;
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                "items",
                "collection",
                [
                    "type" => new ContentInGroupType($this->groupContent, $this->entityManager),
                    "required" => false,
                    "label" => " ",
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
                "data_class" => "AppBundle\\Entity\\Content\\GroupContent"
            ]
        );
    }
}