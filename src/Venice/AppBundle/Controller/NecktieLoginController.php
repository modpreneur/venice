<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 08.10.15
 * Time: 10:11.
 */

namespace Venice\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Event\AppEvents;
use Venice\AppBundle\Event\NecktieLoginSuccessfulEvent;
use Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException;

//use Venice\AppBundle\Interfaces\NecktieGatewayInterface;
//use Venice\AppBundle\Services\NecktieGateway;
//use Venice\AppBundle\Services\NecktieLoginCookieService;
//use Venice\AppBundle\Services\NecktieLoginProcessHelper;
//use Symfony\Component\EventDispatcher\EventDispatcherInterface;
//use Doctrine\ORM\EntityManagerInterface;
//use FOS\UserBundle\Security\LoginManager;

/**
 * Class NecktieLoginController.
 *
 * @Route(service="venice.app.necktie_login_controller")
 */
class NecktieLoginController extends Controller
{
    const REQUESTED_URL_COOKIE_NAME = 'ru';
    const STATE_COOKIE_NAME = 'state';

//todo: waiting for Symfony 3.3... http://m.memegen.com/3qs3c0.jpg
//    /**
//     * @var NecktieLoginCookieService
//     */
//    protected $cookieService;
//
//    /**
//     * @var NecktieLoginProcessHelper
//     */
//    protected $helper;
//
//    /**
//     * @var NecktieGatewayInterface
//     */
//    protected $gateway;
//
//    /**
//     * @var EntityManagerInterface
//     */
//    protected $entityManager;
//
//    /**
//     * @var LoginManager
//     */
//    protected $loginManager;
//
//    /**
//     * @var EventDispatcherInterface
//     */
//    protected $eventDispatcher;

//    /**
//     * NecktieLoginController constructor.
//     * @param NecktieLoginCookieService $cookieService
//     * @param NecktieLoginProcessHelper $helper
//     * @param NecktieGateway $gateway
//     * @param EntityManagerInterface $entityManager
//     */
//    public function __construct(
//        NecktieLoginCookieService $cookieService,
//        NecktieLoginProcessHelper $helper,
//        NecktieGateway $gateway,
//        EntityManagerInterface $entityManager
//    ) {
//        $this->cookieService = $cookieService;
//        $this->helper = $helper;
//        $this->gateway = $gateway;
//        $this->entityManager = $entityManager;
//    }


    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \InvalidArgumentException
     */
    public function redirectToNecktieLoginAction(Request $request): RedirectResponse
    {
        $stateCookie = $this->get('venice.app.necktie_login_cookie_service')
            ->createStateCookie(self::STATE_COOKIE_NAME);

        $response = $this->redirect($this->get('venice.app.necktie_login_process_helper')->getLoginUrl($stateCookie));

        //set the state cookie
        $response->headers->setCookie($stateCookie);

        //create and set the redirect url cookie
        if ($request->getSession() !== null) {
            $redirectUrlCookie = $this->get('venice.app.necktie_login_cookie_service')->createRedirectUrlCookie(
                $request->getSession(),
                self::REQUESTED_URL_COOKIE_NAME
            );

            if ($redirectUrlCookie !== null) {
                $response->headers->setCookie($redirectUrlCookie);
            }
        }

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \LogicException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws UnsuccessfulNecktieResponseException
     */
    public function processNecktieLoginResponseAction(Request $request): Response
    {
        $this->get('venice.app.necktie_login_cookie_service')->validateStateCookie(
            $request->cookies->get(self::STATE_COOKIE_NAME),
            $request->get('state', '')
        );

        $necktieToken = $this->getAccessTokenFromRequest($request);

        if ($necktieToken === null) {
            $this->get('logger')->addEmergency('Could not get token from necktie response while trying to login user');

            //todo: Display proper error page
            return new Response('Could not log in.', 500);
        }

        $user = null;

        try {
            $user = $this->get('venice.app.necktie_login_process_helper')->getAndLoginUser(
                $request->get('access_token'),
                $necktieToken
            );
        } catch (UnsuccessfulNecktieResponseException $e) {
            throw new UnsuccessfulNecktieResponseException(
                'Could not get user from necktie. ' . $e->getMessage(),
                0,
                $e
            );
        }

        try {
            $this->performNecktieCalls($user);
        } catch (UnsuccessfulNecktieResponseException $e) {
            //necktie requests failed, try to refresh access token and perform requests again
            $this->get('venice.app.necktie_gateway')->refreshAccessToken($user);

            $this->performNecktieCalls($user);
        }

        $this->dispatchSuccessfulLoginEvent($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->createResponse($request);
    }

    /**
     * Create response for the login action.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    protected function createResponse(Request $request): RedirectResponse
    {
        if ($request->cookies->has(self::REQUESTED_URL_COOKIE_NAME)) {
            $redirectUrlCookieValue = $request->cookies->get(self::REQUESTED_URL_COOKIE_NAME);
            $serverHost = $request->server->get('HTTP_HOST');

            if ($this->get('venice.app.necktie_login_cookie_service')
                ->validateRedirectUrlCookie($redirectUrlCookieValue, $serverHost)
            ) {
                return $this->redirect($redirectUrlCookieValue);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @param                $user
     *
     * @throws \Venice\AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     * @throws \RuntimeException
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Venice\AppBundle\Exceptions\ExpiredRefreshTokenException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function performNecktieCalls($user): void
    {
        $givenAccesses = $this->get('venice.app.necktie_gateway')->updateProductAccesses($user);
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($givenAccesses as $givenAccess) {
            $entityManager->persist($givenAccess);
        }

        $entityManager->flush();
    }



    /**
     * Get OAuthToken or null from request.
     *
     * @param $request
     *
     * @return OAuthToken|null
     */
    protected function getAccessTokenFromRequest(Request $request)
    {
        //todo:!
        return $this->get('venice.app.necktie_entity_mapper')->createOAuthTokenFromArray($request->query->all());
    }

    /**
     * @param $user
     */
    protected function dispatchSuccessfulLoginEvent($user)
    {
        $this->get('event_dispatcher')
            ->dispatch(AppEvents::NECKTIE_LOGIN_SUCCESSFUL, new NecktieLoginSuccessfulEvent($user));
    }
}
