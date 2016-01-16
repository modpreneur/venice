<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 13:55
 */

namespace AdminBundle\Form\Content;


use AdminBundle\Form\AdminBaseType;
use AdminBundle\Form\DataTransformer\EntityToNumberTransformer;
use AppBundle\Entity\Content\GroupContent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentInGroupType extends AdminBaseType
{
    /** @var  GroupContent */
    protected $groupContent;

    /** @var  EntityManagerInterface */
    protected $entityManager;

    /**
     * ContentInGroupType constructor.
     *
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
                "content",
                EntityType::class,
                [
                    "class" => "AppBundle\\Entity\\Content\\Content",
                    'query_builder' => $this->getQueryBuilderFunction(),
                    "choice_label" => "name",
                    "label" => "Content",
                    "placeholder" => "Choose content",
                    "required" => true
                ]
            )
            ->add(
                "group",
                HiddenType::class,
                [
                    // Uses model transformer
                    "data" => $this->groupContent,
                    "data_class" => null,
                    "label" => false,
                ]
            )
            ->add(
                "delay",
                IntegerType::class,
                [
                    "empty_data" => 0,
                    "required" => false,
                ]
            )
            ->add(
                "orderNumber",
                IntegerType::class,
                [
                    "empty_data" => 0,
                    "required" => false,
                ]
            );

        $builder->get("group")
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "AppBundle:Content\\GroupContent"
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\Content\\ContentInGroup",
            ]
        );
    }


    /**
     * Get query builder function to query entities from database
     *
     * @return \Closure
     */
    protected function getQueryBuilderFunction()
    {
        // The contentGroup entity contains data
        if ($this->groupContent && $this->groupContent->getId()) {
            $groupId = $this->groupContent->getId();
            return function (EntityRepository $er) use ($groupId) {
                return $er
                    ->createQueryBuilder('c')
                    ->andWhere('c.id != :id')
                    ->setParameter("id", $groupId);
            };
        } else {
            return function (EntityRepository $er) {
                return $er->createQueryBuilder("c");
            };
        }
    }
}