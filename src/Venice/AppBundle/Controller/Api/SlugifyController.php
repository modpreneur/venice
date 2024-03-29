<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.02.16
 * Time: 13:58.
 */
namespace Venice\AppBundle\Controller\Api;

use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SlugifyController.
 */
class SlugifyController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function slugifyAction(Request $request)
    {
        $string = $request->request->get('string');
        $slugify = new Slugify();

        return new JsonResponse(['handle' => $slugify->slugify($string)], 200);
    }
}
