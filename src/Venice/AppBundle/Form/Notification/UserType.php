<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 28.04.16
 * Time: 12:43.
 */
namespace Venice\AppBundle\Form\Notification;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trinity\NotificationBundle\Interfaces\NotificationTypeInterface;
use Venice\AppBundle\Entity\User;

/**
 * Class UserType.
 */
class UserType extends AbstractType implements NotificationTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('username')
            ->add('email', EmailType::class)
            ->add('locked')
            ->add('firstName')
            ->add('lastName')
            ->add('phoneNumber')
            ->add('website', UrlType::class, ['required' => false])
            ->add('country', TextType::class)
            ->add('region')
            ->add('city')
            ->add('postalCode')
            ->add('addressLine1')
            ->add('addressLine2')
            ->add('id', IntegerType::class, ['property_path' => 'necktieId']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'csrf_protection' => false,
            ]
        );
    }
}
