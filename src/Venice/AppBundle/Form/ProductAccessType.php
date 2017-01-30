<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.01.16
 * Time: 14:03.
 */
namespace Venice\AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Entity\ProductAccess;
use Venice\AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductAccessType extends BaseType
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
                'product',
                EntityType::class,
                [
                    'class' => Product::class,
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('p')
                            ->orderBy('p.name', 'ASC');
                    },
                    'choice_label' => 'Name',
                    'required' => true,
                ]
            )
            ->add(
                'fromDate',
                DateType::class,
                [
                    'required' => true,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'toDate',
                DateType::class,
                [
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'user',
                HiddenType::class,
                [
                    // Uses model transformer
                    'data' => $options['user'],
                    'data_class' => null,
                    'label' => false,
                ]
            );

        $builder->get('user')
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    'VeniceAppBundle:User'
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
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ProductAccess::class,
            'user' => null,
        ]);
    }
}
