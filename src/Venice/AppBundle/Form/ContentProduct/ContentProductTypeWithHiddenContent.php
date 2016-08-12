<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 01.02.16
 * Time: 17:56
 */

namespace Venice\AppBundle\Form\ContentProduct;

use Venice\AppBundle\Form\DataTransformer\EntityToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentProductTypeWithHiddenContent extends ContentProductType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add(
                'product',
                EntityType::class,
                [
                    'class' => "Venice\AppBundle\\Entity\\Product\\Product",
                    'choice_label' => 'name'
                ]
            )
            ->add(
                'content',
                HiddenType::class,
                [
                      // Uses model transformer
                    'data' => $options['content'],
                    'data_class' => null,
                    'label' => false
                ]
            )
            ->add(
                'delay',
                IntegerType::class,
                [
                    'required' => true,
                    'empty_data' => 0,
                    'attr' => ['placeholder' => 'Delay[hours]']
                ]
            )
            ->add(
                'orderNumber',
                IntegerType::class,
                [
                    'required' => true,
                    'empty_data' => 0,
                    'attr' => ['placeholder' => 'Order number']
                ]
            );

        $builder
            ->get('content')
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    "VeniceAppBundle:Content\\Content"
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('content', null);
    }
}
