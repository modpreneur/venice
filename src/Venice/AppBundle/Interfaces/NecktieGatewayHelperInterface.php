<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 18:53.
 */
namespace Venice\AppBundle\Interfaces;

use Venice\AppBundle\Entity\Interfaces\OAuthTokenInterface;

interface NecktieGatewayHelperInterface
{
    /**
     * @param $response array
     *
     * @return array|null
     */
    public function getUserInfoFromNecktieProfileResponse(array $response);

    /**
     * Create a OAuthToken object from oauth server response converted to array.
     *
     * @param $array array
     *
     * @return OAuthTokenInterface|null
     */
    public function createOAuthTokenFromArray(array $array);

    /**
     * Check if the given response(string or array) is ok.
     * That means if it does not contain oauth error or other error in field "error".
     *
     * @param string|array $response
     *
     * @return bool
     */
    public function isResponseOk($response);

    /**
     * Check if given response(string or array) contains oauth invalid access token error.
     *
     * @param string|array $response
     *
     * @return bool
     */
    public function isAccessTokenInvalidResponse($response);

    /**
     * Check if given response(string or array) contains oauth expired access token error.
     *
     * @param string|array $response
     *
     * @return bool
     */
    public function isAccessTokenExpiredResponse($response);

    /**
     * Check if given response(string or array) contains oauth invalid refresh token error.
     * This error is thrown when the token does not exist or is expired.
     *
     * @param string|array $response
     *
     * @return bool
     */
    public function isRefreshTokenExpiredResponse($response);

    /**
     * @param string|array $response
     *
     * @return bool
     */
    public function isInvalidClientResponse($response);

    /**
     * @param string|array $response
     *
     * @return bool
     */
    public function hasError($response);
}
