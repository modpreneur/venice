<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.02.16
 * Time: 11:59.
 */

namespace Venice\AppBundle\Form\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\User;
use Venice\AppBundle\Form\BaseType;
use Venice\AppBundle\Services\EntityOverrideHandler;
use Venice\AppBundle\Services\RolesLoader;

/**
 * Class RolesType
 */
class RolesType extends BaseType
{
    protected $roles;

    /**
     * RolesType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EntityOverrideHandler  $entityOverrideHandler
     * @param RolesLoader            $rolesLoader
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityOverrideHandler $entityOverrideHandler,
        RolesLoader $rolesLoader
    ) {
        parent::__construct($entityManager, $entityOverrideHandler);

        $this->roles = $rolesLoader->readRolesFile();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add(
            'roles',
            ChoiceType::class,
            [
                'expanded' => true,
                'multiple' => true,
                'choices' => array_keys($this->roles),
                'choice_label' => function ($value) {
                    return $this->roles[$value];
                },
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
        parent::configureOptions($resolver);

        $resolver->setDefault('data_class', User::class);
    }
}
