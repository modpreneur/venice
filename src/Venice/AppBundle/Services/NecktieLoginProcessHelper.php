<?php
declare(strict_types=1);

namespace Venice\AppBundle\Services;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class NecktieLoginProcessHelper
 */
class NecktieLoginProcessHelper
{
    const NECKTIE_OAUTH_AUTH_URI = 'oauth/v2/auth';

    /**
     * @var string
     */
    protected $necktieUrl;

    /**
     * @var string
     */
    protected $necktieClientId;

    /**
     * @var string
     */
    protected $necktieClientSecret;

    /**
     * @var string
     */
    protected $loginResponseRoute;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * NecktieLoginProcessHelper constructor.
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param string $necktieUrl
     * @param string $necktieClientId
     * @param string $necktieClientSecret
     * @param string $loginResponseRoute
     */
    public function __construct(
        RouterInterface $router,
        string $necktieUrl,
        string $necktieClientId,
        string $necktieClientSecret,
        string $loginResponseRoute
    ) {
        $this->necktieUrl = $necktieUrl;
        $this->necktieClientId = $necktieClientId;
        $this->necktieClientSecret = $necktieClientSecret;
        $this->loginResponseRoute = $loginResponseRoute;
        $this->router = $router;
    }


    /**
     * Do not use for redirecting to login form! Redirect to "login_route" route instead!
     *
     * @internal
     *
     * @param Cookie $stateCookie Cookie containing the state string
     *
     * @return string
     *
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    public function getLoginUrl(Cookie $stateCookie): string
    {
        $necktieUrl = $this->necktieUrl . self::NECKTIE_OAUTH_AUTH_URI;
        $queryParameters = [
            'client_id' => $this->necktieClientId,
            'client_secret' => $this->necktieClientSecret,
            'redirect_uri' => $this->router->generate(
                $this->loginResponseRoute,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'grant_type' => 'trusted_authorization',
            'state' => $stateCookie->getValue(),
        ];
        $queryParametersString = \http_build_query($queryParameters);

        return $necktieUrl . '?' . $queryParametersString;
    }
}