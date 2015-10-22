<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 10:11
 */

namespace AppBundle\Controller;


use AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
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
     * @Route("/login", name="necktie_login")
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
     * @Route("/login-response", name="necktie_login_response")
     * @param Request $request
     *
     * @return Response
     * @throws UnsuccessfulNecktieResponseException
     */
    public function processNecktieLoginResponseAction(Request $request)
    {
        $necktieGateway = $this->get("app.services.necktie_gateway");
        $entityManager = $this->getDoctrine()->getManager();
        $cookieValue = $request->cookies->get($necktieGateway::STATE_COOKIE_NAME);

        if(!is_string($cookieValue))
        {
            return new Response("Please, enable cookies in your browser.");
        }

        if($cookieValue !== $request->get("state"))
        {
            throw new AccessDeniedHttpException();
        }

        $necktieToken = $necktieGateway->getHelper()->createAccessTokenFromArray($request->query->all());

        if($necktieToken)
        {
            $user = null;

            try
            {
                $user = $this->getAndLoginUser($necktieGateway, $request, $necktieToken);
            }
            catch(UnsuccessfulNecktieResponseException $e)
            {
                throw new UnsuccessfulNecktieResponseException("Could not get user from necktie.");
            }

            try
            {
                $this->performNecktieCalls($necktieGateway, $user);
            }
            catch(UnsuccessfulNecktieResponseException $e)
            {
                //necktie requests failed
                //try to refresh access token and perform requests again
                $necktieGateway->refreshAccessToken($user);

                $this->performNecktieCalls($necktieGateway, $user);
            }

            $entityManager->persist($user);
            $entityManager->flush();
        }
        else
        {
            return new Response("An error occurred. Please report it to the support.");
        }

        return $this->redirectToRoute("homepage");
    }

    protected function performNecktieCalls($necktieGateway, $user)
    {
        $necktieGateway->updateProductAccesses($user);
        $necktieGateway->getInvoices($user);
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