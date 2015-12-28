<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.12.15
 * Time: 19:16
 */

namespace AdminBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogArticleType extends AdminBaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $currentYear = (new \DateTime())->format("Y");

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
                    "required" => true,
                    "years" => [$currentYear, $currentYear+1, $currentYear+2, $currentYear+3]
                ]
            )

            ->add(
                "content",
                TextareaType::class,
                [
                    "required" => true
                ]
            )
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                "data_class" => "AppBundle\\Entity\\BlogArticle"
            ]
        );
    }
}