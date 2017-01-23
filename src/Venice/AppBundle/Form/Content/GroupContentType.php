<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.11.15
 * Time: 21:08.
 */
namespace Venice\AppBundle\Form\Content;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Entity\Interfaces\GroupContentInterface;
use Venice\AppBundle\Form\Collection\CollectionType;

/**
 * {@inheritdoc}
 */
class GroupContentType extends ContentType
{
    /** @var  GroupContentInterface */
    protected $groupContent;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'handle',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'items',
                CollectionType::class,
                [
                    'type' => ContentInGroupType::class,
                    'options' => ['groupContent' => $options['data']],
                    'required' => false,
                    'label' => 'Contents',
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => GroupContent::class
            ]
        );
    }
}
