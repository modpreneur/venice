<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.11.15
 * Time: 12:21
 */

namespace AdminBundle\Controller;


use AppBundle\Services\FormErrorSerializer;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;

class BaseAdminController extends FOSRestController
{
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
     * @return \AppBundle\Services\CMSProblemHelper
     */
    public function getCMSProblemHelper()
    {
        return $this->get("app.services.cms_problem_helper");
    }


    /**
     * @param Form $form
     * @param      $message
     *
     * @return JsonResponse
     */
    public function returnFormErrorsJsonResponse(Form $form, $message = "")
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
     * @param bool $setDashboard
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs($setDashboard = true)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');

        if($setDashboard)
            $breadcrumbs->addRouteItem("Dashboard", "admin_dashboard");

        return $breadcrumbs;
    }


}