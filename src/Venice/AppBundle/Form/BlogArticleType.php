<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:16.
 */

namespace Venice\AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Venice\AppBundle\Entity\BlogArticle;
use Venice\AppBundle\Entity\Category;
use Venice\AppBundle\Entity\Product\Product;
use Trinity\AdminBundle\Form\FroalaType\FroalaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\Tag;

/**
 * Class BlogArticleType.
 */
class BlogArticleType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $currentYear = (int) (new \DateTime())->format('Y');

        $builder
            ->add(
                'title',
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
            ->add(//todo: @JakubFajkus add a time
                'dateToPublish',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => ['data-limit' => json_encode(['min' => 'now', 'max' => ['year' => $currentYear + 4]])],
                ]
            )
            ->add(
                'products',
                EntityType::class,
                [
                    'class' => Product::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'In products',
                    'required' => false,
                ]
            )
            ->add(
                'categories',
                EntityType::class,
                [
                    'class' => Category::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'In categories',
                    'required' => false,
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'class' => Tag::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('t')
                            ->orderBy('t.name', 'ASC');
                    },
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => true,
                    'label' => 'With tags',
                    'required' => false,
                ]
            )
            ->add(
                'content',
                FroalaType::class,
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
                'data_class' => $this->entityOverrideHandler->getEntityClass(BlogArticle::class),
            ]
        );
    }
}
