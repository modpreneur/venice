<?php

namespace AdminBundle\Services;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormTypeInterface;

class FormFactory
{

    /**
     * Creates a form to edit entity.
     *
     * @param Controller        $controller The controller. It is used to create form and generate URL.
     * @param object            $entity     The entity
     * @param FormTypeInterface $entityType The entity type object
     * @param string            $urlPrefix  The entity url name
     * @param array             $params     Array of params required to generate URL
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createEditForm(Controller $controller, $entity, FormTypeInterface $entityType, $urlPrefix, $params = [])
    {
        $params['id'] = $entity->getId();
        $form = $controller->createForm(
            get_class($entityType),
            $entity,
            [
                'action' => $controller->generateUrl($urlPrefix.'_update', $params),
                'method' => 'PUT',
                'attr' => ['class' => 'edit-form'],
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Edit',
                'attr' => ['class' => 'button edit']
            ]

        );

        return $form;
    }

    /**
     * Creates a form to create entity.
     *
     * @param Controller        $controller The controller. It is used to create form and generate URL.
     * @param object            $entity     The entity
     * @param FormTypeInterface $entityType The entity type object
     * @param string            $urlPrefix  The entity url name
     * @param string[]          $params     Array of params required to generate URL
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(Controller $controller, $entity, FormTypeInterface $entityType, $urlPrefix, $params = [])
    {
        $form = $controller->createForm(
            get_class($entityType),
            $entity,
            [
                'action' => $controller->generateUrl($urlPrefix.'_create', $params),
                'method' => 'POST',
                'attr' => ['class' => 'new-form'],
            ]
        );

        $form->add(
            'submit',
            SubmitType::class,
            [
                'label' => 'Create',
                'attr' => ['class' => 'button create']
            ]

        );

        return $form;
    }

    /**
     * Creates a form to delete entity by id.
     *
     * @param Controller $controller The controller. It is used to create form and generate URL.
     * @param int        $id         The entity id
     * @param string     $urlPrefix  The entity url name
     * @param string[]   $params     Array of params required to generate URL
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createDeleteForm(Controller $controller, $urlPrefix, $id, $params = [])
    {
        $params['id'] = $id;

        return $controller->createFormBuilder(null, ['attr' => ['class' => 'delete-form']])
            ->setAction(
                $controller->generateUrl($urlPrefix.'_delete', $params)
            )
            ->setMethod(
                'DELETE'
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Delete',
                    'attr' => [
                        'class' => 'button delete',
                        'onclick' => "return confirm('Are you sure?')",
                    ],
                ]
            )->getForm();
    }
}

