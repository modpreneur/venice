<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.02.16
 * Time: 12:23
 */

namespace Venice\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ProductController
 * @package Venice\AppBundle\Controller\Api
 */
class ProductController extends Controller
{
    /**
     * @param $id
     *
     * @return JsonResponse
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \LogicException
     */
    public function necktieProductExistsAction($id)
    {
        return new JsonResponse($this->get('venice.app.necktie_gateway')->productExists($this->getUser(), $id));
    }
}