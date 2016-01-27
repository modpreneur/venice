<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:21
 */

namespace AdminBundle\Controller;


use AdminBundle\Services\FormCreator;
use AppBundle\Services\AppLogic;
use AppBundle\Services\FormErrorSerializer;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseAdminController extends FOSRestController
{
    /** @var  AppLogic */
    protected $logic;

    /** @var  FormCreator */
    protected $formCreator;

    /**
     * @return ObjectManager
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
        return $this->get("form_error_serializer");
    }


    /**
     * @return AppLogic
     */
    public function getLogic():AppLogic
    {
        // save logic to variable to speed up the process a bit
        if(!$this->logic) {
            $this->logic = $this->get("app_logic");
        }

        return $this->logic;
    }

    public function getFormCreator()
    {
        // save creator to variable to speed up the process a bit
        if(!$this->formCreator) {
            $this->formCreator = $this->get("admin.form_creator");
        }

        return $this->formCreator;
    }


    /**
     * @param FormInterface $form
     * @param string $message
     *
     * @return JsonResponse
     */
    public function returnFormErrorsJsonResponse(FormInterface $form, $message = "")
    {
        $errors = $this->getFormErrorSerializer()->serializeFormErrors($form, true, true);

        return new JsonResponse(
            [
                "error" => $errors,
                "message" => $message
            ],
            400
        );
    }

    /**
     * @param bool $setHome
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs($setHome = true)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');

        if($setHome)
            $breadcrumbs->addRouteItem("Home", "admin_dashboard");

        return $breadcrumbs;
    }

}