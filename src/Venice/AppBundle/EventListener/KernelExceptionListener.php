<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.10.15
 * Time: 20:23
 */

namespace Venice\AppBundle\EventListener;


use Venice\AppBundle\Exceptions\ExpiredRefreshTokenException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

class KernelExceptionListener
{
    protected $router;

    protected $container;

    public function __construct(RouterInterface $router, ContainerInterface $container)
    {
        $this->router = $router;
        $this->container = $container;
    }
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        // OAuth refresh token has expired. Force user to login.
        if($event->getException() instanceof ExpiredRefreshTokenException)
        {
            $response = new RedirectResponse(
                $this->router->generate(
                    $this->container->getParameter("login_route"))
            );

            //Set the response and stop event propagation to another listeners.
            $event->setResponse($response);
        }
    }
}