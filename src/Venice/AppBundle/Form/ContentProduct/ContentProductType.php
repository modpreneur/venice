<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 29.11.15
 * Time: 16:33.
 */

namespace Venice\AppBundle\Form\ContentProduct;

use Venice\AppBundle\Entity\ContentProduct;
use Venice\AppBundle\Form\BaseType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ContentProductType extends BaseType
{
    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ContentProduct::class,
            ]
        );
    }
}
