<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 01.02.16
 * Time: 17:57.
 */
namespace Venice\AppBundle\Form\ContentProduct;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Venice\AppBundle\Entity\Content\Content;
use Venice\AppBundle\Entity\Product\Product;
use Venice\AppBundle\Form\DataTransformer\EntityToNumberTransformer;

/**
 * Class ContentProductTypeWithHiddenProduct.
 */
class ContentProductTypeWithHiddenProduct extends ContentProductType
{
    /**
     * {@inheritdoc}
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
                    'choice_label' => 'name',
                ]
            )
            ->add(
                'product',
                HiddenType::class,
                [
                    // Uses model transformer
                    'data' => $options['product'],
                    'data_class' => null,
                    'label' => null,
                ]
            )
            ->add(
                'delay',
                IntegerType::class,
                [
                    'required' => true,
                    'empty_data' => 0,
                    'attr' => ['placeholder' => 'Delay[hours]'],
                ]
            )
            ->add(
                'orderNumber',
                IntegerType::class,
                [
                    'required' => true,
                    'empty_data' => 0,
                    'attr' => ['placeholder' => 'Order number'],
                ]
            );

        $builder
            ->get('product')
            ->addModelTransformer(
                new EntityToNumberTransformer(
                    $this->entityManager,
                    Product::class
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

        $resolver->setDefault('product', null);
    }
}
