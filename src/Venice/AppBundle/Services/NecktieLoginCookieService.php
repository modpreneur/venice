<?php

namespace Venice\AppBundle\Services;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class NecktieLoginCookieService
 */
class NecktieLoginCookieService
{
    /**
     * @param string $cookieName
     *
     * @return Cookie
     * @throws \InvalidArgumentException
     */
    public function createStateCookie(string $cookieName) : Cookie
    {
        return new Cookie($cookieName, \hash('sha256', \mt_rand() + \time()));
    }

    /**
     * Validate cookie and it's value with the "state" string.
     *
     * @param Cookie $cookie Cookie containing the state string.
     * @param string $state The state string from the request.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException When the cookie is invalid
     */
    public function validateStateCookie(Cookie $cookie, string $state): void
    {
        $cookieValue = $cookie->getValue();

        if (!\is_string($cookieValue)) {
            throw new AccessDeniedHttpException('Could not load value from a cookie');
        }

        if ($cookieValue !== $state) {
            throw new AccessDeniedHttpException('Invalid state value');
        }
    }

    /**
     * Check if the host from the cookie is the host of the application.
     *
     * @param string $cookieRedirectValue
     * @param string $serverHttpHost
     *
     * @return bool
     */
    public function validateRedirectUrlCookie(string $cookieRedirectValue, string $serverHttpHost): bool
    {
        $serverHost = \parse_url($serverHttpHost, \PHP_URL_HOST);
        $cookieHost = \parse_url($cookieRedirectValue, \PHP_URL_HOST);

        return $serverHost === $cookieHost;
    }

    /**
     * Create the cookie containing the original request url.
     *
     * @param SessionInterface $session
     * @param string $cookieName
     *
     * @return null|Cookie
     *
     * @throws \InvalidArgumentException
     */
    public function createRedirectUrlCookie(SessionInterface $session, string $cookieName): ?Cookie
    {
        //get the original requested url
        foreach ($session->all() as $sessionName => $sessionValue) {
            if (\strpos($sessionName, 'target_path') !== false) {
                return new Cookie($cookieName, $sessionValue);
            }
        }

        return null;
    }
}