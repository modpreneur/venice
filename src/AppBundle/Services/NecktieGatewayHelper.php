<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 22.10.15
 * Time: 14:31
 */

namespace AppBundle\Services;


use AppBundle\Entity\OAuthToken;
use AppBundle\Interfaces\NecktieGatewayHelperInterface;

class NecktieGatewayHelper implements NecktieGatewayHelperInterface
{
    const NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided has expired."}';
    const NECKTIE_INVALID_ACCESS_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"The access token provided is invalid."}';
    //this message is the same for expired as well as invalid refresh token
    const NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR = '{"error":"invalid_grant","error_description":"Invalid refresh token"}';


    /**
     * @inheritdoc
     */
    public function getUserInfoFromNecktieProfileResponse($response)
    {
        $requiredFields = ["username", "email", "id"];
        $userInfo = [];
        $responseContent = json_decode($response, true);

        if(array_key_exists("user", $responseContent))
        {
            $responseContent = $responseContent["user"];
        }

        foreach($requiredFields as $requiredFiled)
        {
            if(array_key_exists($requiredFiled, $responseContent))
            {
                $userInfo[$requiredFiled] = $responseContent[$requiredFiled];
            }
            else
            {
                return null;
            }
        }

        return $userInfo;
    }


    /**
     * @inheritdoc
     */
    public function createAccessTokenFromArray($array)
    {
        if(array_key_exists("access_token", $array)
           && array_key_exists("refresh_token", $array)
           && array_key_exists("scope", $array)
           && array_key_exists("expires_in", $array))
        {
            $necktieToken = new OAuthToken();
            $necktieToken->setAccessToken($array["access_token"]);
            $necktieToken->setRefreshToken($array["refresh_token"]);
            $necktieToken->setScope($array["scope"]);
            $necktieToken->setValidToByLifetime($array["expires_in"]);

            return $necktieToken;
        }
        else
        {
            return null;
        }
    }


    /**
     * @inheritdoc
     */
    public function isResponseOk($response)
    {
        return !($this->isAccessTokenExpiredResponse($response)
                 || $this->isAccessTokenInvalidResponse($response)
                 || $this->isRefreshTokenExpiredResponse($response)
        );
    }


    /**
     * @param string|array $response
     *
     * @return bool
     */
    public function isAccessTokenInvalidResponse($response)
    {
        if((is_string($response) && $response == self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR)
           || (is_array($response) && $response == json_decode(self::NECKTIE_INVALID_ACCESS_TOKEN_ERROR, true))
        )
        {
            return true;
        } else
        {
            return false;
        }
    }


    /**
     * @param string|array $response
     *
     * @return bool
     */
    public function isAccessTokenExpiredResponse($response)
    {
        if((is_string($response) && $response == self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR)
           || (is_array($response) && $response == json_decode(self::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR, true))
        )
        {
            return true;
        } else
        {
            return false;
        }

    }


    /**
     * @param string|array $response
     *
     * @return bool
     */
    public function isRefreshTokenExpiredResponse($response)
    {
        if((is_string($response) && $response == self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR)
           || (is_array($response) && $response == json_decode(self::NECKTIE_EXPIRED_REFRESH_TOKEN_ERROR, true))
        )
        {
            return true;
        } else
        {
            return false;
        }
    }
}