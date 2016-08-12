<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 20.02.16
 * Time: 9:56
 */

namespace Venice\AppBundle\Controller\Froala;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FroalaController
 */
class FroalaController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadFileAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $uploader = $this->get('venice.app.file_uploader');
        $key = $uploader->upload($file);

        $url = $uploader->genUrl($key);

        return new JsonResponse(['link' => $url]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadImageAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $uploader = $this->get('venice.app.file_uploader');
        $key = $uploader->upload($file);

        $url = $uploader->genUrl($key);

        return new JsonResponse(['link' => $url]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteImageAction(Request $request)
    {

    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loadImagesAction(Request $request)
    {

    }
}
