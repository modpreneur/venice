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
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentInGroupType extends AdminBaseType
{
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
                    'query_builder' => $this->getQueryBuilderFunction($options["groupContent"]),
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
                    "data" => $options["groupContent"],
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
                "groupContent" => null
            ]
        );
    }


    /**
     * Get query builder function to query entities from database
     *
     * @param GroupContent $groupContent
     *
     * @return \Closure
     */
    protected function getQueryBuilderFunction(GroupContent $groupContent = null)
    {
        // If the contentGroup entity contains data
        // Get all ContentGroups but the given group - do not allow circular relations
        if ($groupContent && $groupContent->getId()) {
            $groupId = $groupContent->getId();
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