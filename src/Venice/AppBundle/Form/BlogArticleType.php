<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:16
 */

namespace Venice\AppBundle\Form;


use Venice\AppBundle\Entity\Product\Product;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogArticleType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $currentYear = (int)(new \DateTime())->format("Y");

        $builder
            ->add(
                "title",
                TextType::class,
                [
                    "required" => true
                ]
            )
            ->add(
                "handle",
                TextType::class,
                [
                    "required" => false
                ]
            )
            ->add(
                "dateToPublish",
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    "required" => true,
                    'attr' => ['data-limit' => json_encode(['min' =>'now','max'=>['year'=>$currentYear+4]])],
                ]
            )
            ->add(
                "products",
                EntityType::class,
                [
                    "class" => Product::class,
                    "choice_label" => "name",
                    "multiple" => true,
                    "expanded" => true,
                    "label" => "In products",
                ]
            )

            ->add(
                "content",
                FroalaEditorType::class,
                [
                    "required" => false,
                ]
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "Venice\AppBundle\\Entity\\BlogArticle",
            ]
        );
    }
}