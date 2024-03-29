<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.01.16
 * Time: 12:14.
 */
namespace Venice\AppBundle\Services;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutSuccessHandler.
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    /** @var  string */
    protected $necktieUrl;

    /** @var  RouterInterface */
    protected $router;

    /**
     * LogoutSuccessHandler constructor.
     *
     * @param string          $necktieUrl
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, $necktieUrl)
    {
        $this->router = $router;
        $this->necktieUrl = $necktieUrl;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function onLogoutSuccess(Request $request)
    {
        $router = $this->router;
        if ($this->necktieUrl) {
            $url = $this->router->generate('necktie_login', [], $router::ABSOLUTE_URL);

            return new RedirectResponse($this->necktieUrl."/logout?r=$url");
        }

        return new RedirectResponse($this->router->generate('public'));
    }
}
