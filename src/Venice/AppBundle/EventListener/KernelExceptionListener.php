<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:23
 */

namespace Venice\AppBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;

class KernelExceptionListener
{
    /** @var RouterInterface */
    protected $router;

    /** @var string */
    protected $loginRoute;

    public function __construct(RouterInterface $router, string $loginRoute)
    {
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \InvalidArgumentException
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // OAuth refresh token has expired. Force user to login.
        if ($event->getException() instanceof ExpiredRefreshTokenException) {
            $response = new RedirectResponse($this->router->generate($this->loginRoute));

            //Set the response and stop event propagation to another listeners.
            $event->setResponse($response);
        }
    }
}
