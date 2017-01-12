<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 13:27.
 */
namespace Venice\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Venice\AppBundle\Entity\Content\GroupContent;

/**
 * Class FrontController.
 */
class FrontController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $entityClass = $this->get('venice.app.entity_override_handler')->getEntityClass(GroupContent::class);
        $groups = $this->getDoctrine()->getRepository($entityClass)->findAll();

        return $this->render(
            'VeniceFrontBundle:Front:index.html.twig',
            ['groups' => $groups]
        );
    }
}
