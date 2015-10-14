<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 10:11
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 *
 * Class NecktieLoginController
 * @package AppBundle\Controller
 */
class NecktieLoginController extends Controller
{
    /**
     * @Route("/login", name="core_login")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToNecktieLoginAction(Request $request)
    {
        $necktieGateway = $this->get("app.services.necktie_gateway");

        $url = $necktieGateway->getRedirectUrlToNecktieLogin();
        $cookie = $necktieGateway->getStateCookie();

        $response = $this->redirect($url);
        $response->headers->setCookie($cookie);

        return $response;
    }


    /**
     * @Route("/login-response", name="core_login_response")
     * @param Request $request
     *
     * @return Response
     */
    public function processNecktieLoginResponseAction(Request $request)
    {
        $necktieGateway = $this->get("app.services.necktie_gateway");
        $cookieValue = $request->cookies->get($necktieGateway::STATE_COOKIE_NAME);

        if(!is_string($cookieValue))
        {
            return new Response("Please, enable cookies in your browser.");
        }

        if($cookieValue !== $request->get("state"))
        {
            throw new AccessDeniedHttpException();
        }

        if($request->query->get("access_token"))
        {
            $user = $necktieGateway->getUserByAccessToken($request->query->get("access_token"), true);
            $this->get("fos_user.security.login_manager")->logInUser("main", $user);
        }

        $necktieGateway->updateProductAccesses($request->query->get("access_token"));

        return $this->redirectToRoute("homepage");
    }


}