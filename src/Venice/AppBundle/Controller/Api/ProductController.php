<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.02.16
 * Time: 12:23
 */

namespace Venice\AppBundle\Controller\Api;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function necktieProductExistsAction($id)
    {
        return new JsonResponse($this->get("app.services.necktie_gateway")->productExists($this->getUser(), $id));
    }
}