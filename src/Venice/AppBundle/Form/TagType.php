<?php
/**
 * Created by PhpStorm.
 * User: marek
 * Date: 25/01/17
 * Time: 16:56.
 */

namespace Venice\AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\Tag;

/**
 * Class CategoryType.
 */
class TagType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'handle',
                TextType::class,
                [
                    'required' => false,
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->entityOverrideHandler->getEntityClass(Tag::class),
            ]
        );
    }
}
