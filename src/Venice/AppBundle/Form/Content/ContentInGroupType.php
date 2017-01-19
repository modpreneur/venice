<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 13:55.
 */
namespace Venice\AppBundle\Form\Content;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Content\ContentInGroup;
use Venice\AppBundle\Entity\Content\GroupContent;
use Venice\AppBundle\Entity\Interfaces\GroupContentInterface;
use Venice\AppBundle\Form\BaseType;
use Venice\AppBundle\Form\DataTransformer\EntityToNumberTransformer;

class ContentInGroupType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @throws \Symfony\Component\Form\Exception\InvalidArgumentException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'content',
                EntityType::class,
                [
                    'class' => Content::class,
                    'query_builder' => $this->getQueryBuilderFunction($options['groupContent']),
                    'choice_label' => 'name',
                    'label' => 'Content',
                    'placeholder' => 'Choose content',
                    'required' => true,
                ]
            )
            ->add(
                'group',
                HiddenType::class,
                [
                    // Uses model transformer
                    'data' => $options['groupContent'],
                    'data_class' => null,
                    'label' => false,
                ]
            )
            ->add(
                'delay',
                IntegerType::class,
                [
                    'empty_data' => 0,
                    'required' => false,
                    'attr' => ['placeholder' => 'Delay[hours]'],
                ]
            )
            ->add(
                'orderNumber',
                IntegerType::class,
                [
                    'empty_data' => 0,
                    'required' => false,
                    'attr' => ['placeholder' => 'Order number'],
                ]
            );

        $builder->get('group')
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    GroupContent::class
                )
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
                'data_class' => ContentInGroup::class,
                'groupContent' => null,
            ]
        );
    }

    /**
     * Get query builder function to query entities from database.
     *
     * @param GroupContentInterface $groupContent
     *
     * @return \Closure
     */
    protected function getQueryBuilderFunction(GroupContentInterface $groupContent = null)
    {
        // If the contentGroup entity contains data
        // Get all ContentGroups but the given group - do not allow circular relations
        if ($groupContent && $groupContent->getId()) {
            $groupId = $groupContent->getId();

            return function (EntityRepository $entityRepository) use ($groupId) {
                return $entityRepository
                    ->createQueryBuilder('c')
                    ->andWhere('c.id != :id')
                    ->setParameter('id', $groupId);
            };
        } else {
            return function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('c');
            };
        }
    }
}
