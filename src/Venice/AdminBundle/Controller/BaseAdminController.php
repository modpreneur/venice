<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:21.
 */
namespace Venice\AdminBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Venice\AppBundle\Services\AppLogic;
use Venice\AppBundle\Services\EntityFormMatcher;
use Venice\AppBundle\Services\EntityOverrideHandler;
use Venice\AppBundle\Services\FormCreator;
use Venice\AppBundle\Services\FormErrorSerializer;
use Venice\AppBundle\Services\FormOverrideHandler;

/**
 * {@inheritdoc}
 */
class BaseAdminController extends FOSRestController
{
    /** @var AppLogic */
    private $logic;

    /** @var FormCreator */
    private $formCreator;

    /** @var EntityFormMatcher */
    private $formMatcher;

    /** @var FormOverrideHandler */
    private $formOverrideHandler;

    /** @var EntityOverrideHandler */
    private $entityOverrideHandler;

    /**
     * @return ObjectManager
     *
     * @throws \LogicException
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return FormErrorSerializer
     */
    public function getFormErrorSerializer()
    {
        return $this->get('venice.app.form_error_serializer');
    }

    /**
     * @return AppLogic
     */
    public function getLogic():AppLogic
    {
        // save logic to variable to speed up the process a bit
        if (!$this->logic) {
            $this->logic = $this->get('venice.app.app_logic');
        }

        return $this->logic;
    }

    /**
     * @return FormCreator
     */
    public function getFormCreator():FormCreator
    {
        // save creator to variable to speed up the process a bit
        if (!$this->formCreator) {
            $this->formCreator = $this->get('venice.admin.form_creator');
        }

        return $this->formCreator;
    }

    /**
     * @return EntityFormMatcher
     */
    public function getEntityFormMatcher():EntityFormMatcher
    {
        // save creator to variable to speed up the process a bit
        if (!$this->formMatcher) {
            $this->formMatcher = $this->get('venice.app.entity_form_matcher');
        }

        return $this->formMatcher;
    }

    /**
     * @return FormOverrideHandler
     */
    public function getFormOverrideHandler():FormOverrideHandler
    {
        // save creator to variable to speed up the process a bit
        if (!$this->formOverrideHandler) {
            $this->formOverrideHandler = $this->get('venice.app.form_override_handler');
        }

        return $this->formOverrideHandler;
    }

    /**
     * @return EntityOverrideHandler
     */
    public function getEntityOverrideHandler():EntityOverrideHandler
    {
        // save creator to variable to speed up the process a bit
        if (!$this->entityOverrideHandler) {
            $this->entityOverrideHandler = $this->get('venice.app.entity_override_handler');
        }

        return $this->entityOverrideHandler;
    }

    /**
     * @param FormInterface $form
     * @param string        $message
     *
     * @return JsonResponse
     */
    public function returnFormErrorsJsonResponse(FormInterface $form, $message = '')
    {
        $errors = $this->getFormErrorSerializer()->serializeFormErrors($form, true, true);

        return new JsonResponse(
            [
                'error' => $errors,
                'message' => $message,
            ],
            400
        );
    }

    /**
     * @param bool $setHome
     *
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs($setHome = true)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');

        if ($setHome) {
            $breadcrumbs->addRouteItem('Home', 'admin_dashboard');
        }

        return $breadcrumbs;
    }
}
