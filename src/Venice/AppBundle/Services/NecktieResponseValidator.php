<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 14:31.
 */
namespace Venice\AppBundle\Services;

/**
 * Class NecktieResponseValidator
 * @package Venice\AppBundle\Services
 */
class NecktieResponseValidator
{
    const NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR =
        '{"error":"invalid_grant","error_description":"The access token provided has expired."}'
    ;
    const NECKTIE_INVALID_ACCESS_TOKEN_ERROR =
        '{"error":"invalid_grant","error_description":"The access token provided is invalid."}'
    ;
    //this message is the same for expired as well as invalid refresh token
    const NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR =
        '{"error":"invalid_grant","error_description":"Invalid refresh token"}'
    ;
    const NECKTIE_INVALID_CLIENT_ERROR =
        '{"error":"invalid_token","error_description":"The client credentials are invalid"}'
    ;

    /**
     * {@inheritdoc}
     */
    public function isResponseOk($response): bool
    {
        return !($this->isAccessTokenExpiredResponse($response)
            || $this->isAccessTokenInvalidResponse($response)
            || $this->isRefreshTokenExpiredResponse($response)
            || $this->isInvalidClientResponse($response)
            || $this->hasError($response)
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isAccessTokenInvalidResponse($response): bool
    {
        return (
            (\is_string($response) && false !== \strpos($response, self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR))
            || (\is_array($response) && ($response === \json_decode(self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR, true))
            )
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isAccessTokenExpiredResponse($response): bool
    {
        return ((\is_string($response) && false !== \strpos($response, self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR))
            || (\is_array($response) && $response === \json_decode(self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR, true))
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenExpiredResponse($response): bool
    {
        return (
            (\is_string($response) && false !== \strpos($response, self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR))
            || (\is_array($response) && $response === \json_decode(self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR, true))
        );
    }


    /**
     * {@inheritdoc}
     */
    public function isInvalidClientResponse($response): bool
    {
        return (
            (\is_string($response) && false !== \strpos($response, self::NECKTIE_INVALID_CLIENT_ERROR))
            || (\is_array($response) && $response === \json_decode(self::NECKTIE_INVALID_CLIENT_ERROR, true))
        );
    }


    /**
     * {@inheritdoc}
     */
    public function hasError($response): bool
    {
        return (
            (\is_string($response) && false !== \strpos($response, 'error'))
            || (\is_array($response) && \array_key_exists('error', $response))
        );
    }
}
