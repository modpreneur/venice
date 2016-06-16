<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 10:11
 */

namespace Venice\AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Event\AppEvents;
use Venice\AppBundle\Event\NecktieLoginSuccessfulEvent;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;
use Venice\AppBundle\Services\NecktieGateway;

/**
 *
 * Class NecktieLoginController
 * @package Venice\AppBundle\Controller
 */
class NecktieLoginController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToNecktieLoginAction()
    {
        $necktieGateway = $this->getGateway();

        $url = $necktieGateway->getLoginUrl();
        $cookie = $necktieGateway->getStateCookie();

        $response = $this->redirect($url);
        $response->headers->setCookie($cookie);

        return $response;
    }


    /**
     * @param Request $request
     *
     * @return Response
     * @throws UnsuccessfulNecktieResponseException
     */
    public function processNecktieLoginResponseAction(Request $request)
    {
        /** @var NecktieGateway $necktieGateway */
        $necktieGateway = $this->getGateway();
        $entityManager = $this->getDoctrine()->getManager();

        $this->validateCookie($necktieGateway, $request);

        $necktieToken = $this->getAccessTokenFromRequest($necktieGateway, $request);

        if ($necktieToken) {
            $user = null;

            try {
                $user = $this->getAndLoginUser($necktieGateway, $request, $necktieToken);
            } catch (UnsuccessfulNecktieResponseException $e) {
                throw new UnsuccessfulNecktieResponseException("Could not get user from necktie." . $e->getMessage());
            }

            try {
                $this->performNecktieCalls($necktieGateway, $user);
            } catch (UnsuccessfulNecktieResponseException $e) {
                //necktie requests failed
                //try to refresh access token and perform requests again
                $necktieGateway->refreshAccessToken($user);

                $this->performNecktieCalls($necktieGateway, $user);
            }

            $this->dispatchSuccessfulLoginEvent($user);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("homepage");

        } else {
            return new Response("An error occurred. Please report it to the support.");
        }
    }


    /**
     * @param NecktieGateway $necktieGateway
     * @param $user
     */
    protected function performNecktieCalls(NecktieGateway $necktieGateway, $user)
    {
        $necktieGateway->updateProductAccesses($user);
    }


    /**
     * Get user from necktie by access token and log him in
     *
     * @param NecktieGateway $necktieGateway
     * @param Request $request
     * @param OAuthToken $necktieToken
     * 
     * @return \Venice\AppBundle\Entity\User|null
     */
    protected function getAndLoginUser(NecktieGateway $necktieGateway, Request $request, OAuthToken $necktieToken)
    {
        $user = $necktieGateway->getUserByAccessToken($request->query->get("access_token"), true);

        if ($user === null) {

            throw new NotFoundHttpException("User not found!");
        }

        $this->getLoginManager()->logInUser("main", $user);

        $necktieToken->setUser($user);
        $user->addOAuthToken($necktieToken);

        return $user;
    }


    /**
     * Validate cookie and it's value with the "state" parameter from query string
     *
     * @param NecktieGateway $necktieGateway
     * @param Request $request
     *
     * @return Response|null
     */
    protected function validateCookie(NecktieGateway $necktieGateway, Request $request)
    {
        $cookieValue = $request->cookies->get($necktieGateway::STATE_COOKIE_NAME);

        if (!is_string($cookieValue)) {
            return new AccessDeniedHttpException("Please, enable cookies in your browser.");
        }

        if ($cookieValue !== $request->get("state")) {
            throw new AccessDeniedHttpException();
        }
    }


    /**
     * Get OAuthToken or null from request
     *
     * @param $request
     * @param $necktieGateway
     * @return OAuthToken|null
     */
    protected function getAccessTokenFromRequest(NecktieGateway $necktieGateway, Request $request)
    {
        return $necktieGateway->getHelper()->createOAuthTokenFromArray($request->query->all());
    }


    /**
     * Get login manager
     *
     * @return \FOS\UserBundle\Security\LoginManager
     */
    protected function getLoginManager()
    {
        return $this->get("fos_user.security.login_manager");
    }


    /**
     * @return NecktieGateway
     */
    protected function getGateway()
    {
        return $this->get("app.services.necktie_gateway");
    }


    protected function dispatchSuccessfulLoginEvent($user)
    {
        $this->get("event_dispatcher")
            ->dispatch(AppEvents::NECKTIE_LOGIN_SUCCESSFUL, new NecktieLoginSuccessfulEvent($user));
    }

}