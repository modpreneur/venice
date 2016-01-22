<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.01.16
 * Time: 14:03
 */

namespace AdminBundle\Form;


use AdminBundle\Form\DataTransformer\EntityToNumberTransformer;
use AppBundle\Entity\User;
use AppBundle\Form\BaseType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAccessType extends BaseType
{
    /** @var User */
    protected $user;

    protected $entityManager;

    public function __construct(User $user, $entityManager)
    {
        $this->user = $user;
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
                "product",
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\Product\\Product",
                    "choice_label" => "Name",
                    "required" => true
                ]
            )
            ->add(
                "fromDate",
                DateType::class,
                [
                    "required" => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                "toDate",
                DateType::class,
                [
                    "required" => false,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                "user",
                HiddenType::class,
                [
                    // Uses model transformer
                    "data" => $this->user,
                    "data_class" => null,
                    "label" => false,
                ]
            );

        $builder->get("user")
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "AppBundle:User"
                )
            );
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "data_class" => "AppBundle\\Entity\\ProductAccess"
        ]);
    }
}