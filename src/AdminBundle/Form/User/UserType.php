<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.12.15
 * Time: 22:07
 */

namespace AdminBundle\Form\User;


use AdminBundle\Form\AdminBaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AdminBaseType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username")
            ->add("email", "email")
            ->add("plainPassword", "repeated", [
                "type" => "password",
                "options" => ["translation_domain" => "FOSUserBundle"],
                "first_options" => ["label" => "Password"],
                "second_options" => ["label" => "Repeat Password"],
                "invalid_message" => "Password fields do not match",
                "required" => false,
            ])
            ->add("public")
            ->add("locked", null, ["required" => false])
            ->add("firstName")
            ->add("lastName")
            ->add("phoneNumber")
            ->add("website", "url", ["required" => false])
            ->add("country", "country", [
                "empty_value" => "Choose an option",
                "preferred_choices" => ["US", "CZ"],
                "required" => false,
            ])
            ->add("region")
            ->add("city")
            ->add("postalCode")
            ->add("addressLine1")
            ->add("addressLine2");
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => "AppBundle\\Entity\\User",
        ]);
    }
}