<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 20.02.16
 * Time: 9:56
 */

namespace AppBundle\Controller\Froala;


use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FroalaController
 *
 * @Route("/froala")
 */
class FroalaController extends Controller
{
    /**
     * @Route("/upload/file", name="app_froala_upload_file")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadFileAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get("file");
        $uploader = $this->get("app.file_uploader");
        $key = $uploader->upload($file);

        $url = $uploader->genUrl($key);

        return new JsonResponse(["link" => $url]);
    }

    /**
     * @Route("/upload/image", name="app_froala_upload_image")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadImageAction(Request $request)
    {
        /** @var UploadedFile $file */
        $file = $request->files->get("file");
        $uploader = $this->get("app.file_uploader");
        $key = $uploader->upload($file);

        $url = $uploader->genUrl($key);

        return new JsonResponse(["link" => $url]);
    }

    /**
     * @Route("/delete/image", name="app_froala_delete_image")
     * @param Request $request
     * @return Response
     */
    public function deleteImageAction(Request $request)
    {

    }

    /**
     * @Route("/delete/image", name="app_froala_load_images")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function loadImagesAction(Request $request)
    {

    }
}