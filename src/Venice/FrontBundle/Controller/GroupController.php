<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 17.01.16
 * Time: 13:14
 */

namespace Venice\FrontBundle\Controller;


use Venice\AppBundle\Entity\Content\GroupContent;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("front/group")
 *
 * Class GroupController
 */
class GroupController extends Controller
{
    /**
     * @Route("/{id}", name="front_group_show")
     *
     * @param GroupContent $group
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showGroupAction(GroupContent $group)
    {
        return $this->render(
            "VeniceFrontBundle:Content:uglyGroup.html.twig",
            [
                "group" => $group
            ]
        );
    }
}