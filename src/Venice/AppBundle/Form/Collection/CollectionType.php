<?php

namespace Venice\AppBundle\Form\Collection;

use Nette\Utils\Random;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {@inheritdoc}
 */
class CollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_add'] && $options['prototype']) {
            // To be unique for every level. Must be generated here
            $options['prototype_name'] = '__name'.Random::generate(5).'__';

            $prototype = $builder->create($options['prototype_name'], $options['type'], array_replace([
                'label' => $options['prototype_name'].'label__',
            ], $options['options']));
            $builder->setAttribute('prototype', $prototype->getForm());
            //Add data options attribute with valid prototype_name
            $options['dataOptions']['prototype_name'] = $options['prototype_name'];
            $builder->setAttribute('dataOptions', json_encode($options['dataOptions']));
        }

        $resizeListener = new ResizeFormListener(
            $options['type'],
            $options['options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'allow_add' => $options['allow_add'],
            'allow_delete' => $options['allow_delete'],
        ]);

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
            $view->vars['dataOptions'] = $form->getConfig()->getAttribute('dataOptions');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->hasAttribute('prototype') && $view->vars['prototype']->vars['multipart']) {
            $view->vars['multipart'] = true;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'allow_add' => false,
            'allow_delete' => false,
            'prototype' => true,
            'prototype_name' => null,
            'type' => 'text',
            'block_name' => 'entry',
            'options' => [],
            'dataOptions' => [],
            'delete_empty' => false,
        ]);
    }
}
