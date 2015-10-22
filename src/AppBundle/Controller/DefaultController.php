<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product\Product;
use AppBundle\Exceptions\UnsuccessfulNecktieResponse;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * This is just a sandbox controller.
 *
 * Class DefaultController
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     *
     *
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $necktieGateway = $this->get("app.services.necktie_gateway");
        $user = $this->getUser();

        $necktieGateway->updateProductAccesses($user);
        $necktieGateway->getInvoices($user);

        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }



    protected function performNecktieCalls($necktieGateway, $user)
    {
        $necktieGateway->updateProductAccesses($user);
        //$necktieGateway->getInvoices($user);
    }

    public function getAndLoginUser($necktieGateway, $request, $necktieToken)
    {
        $user = $necktieGateway->getUserByAccessToken($request->query->get("access_token"));
        $this->get("fos_user.security.login_manager")->logInUser("main", $user);
        $necktieToken->setUser($user);
        $user->addNecktieToken($necktieToken);

        return $user;
    }
}
