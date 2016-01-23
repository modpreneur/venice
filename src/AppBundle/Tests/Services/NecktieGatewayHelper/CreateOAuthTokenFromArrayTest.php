<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 17:07
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


use AppBundle\Entity\OAuthToken;

class CreateOAuthTokenFromArrayTest extends BaseTest
{
    const ACCESS_TOKEN = "ZTcxZDNmYjU1ZTQ2NWExYmQ0YTRmMDBiZGJmZDEyOTBkNzhiYWQyNWQxM2UyZTMzMjQwMzY5OGRlODVhNzAwNw";
    const REFRESH_TOKEN = "ODBmZGRlZDUwYjRjY2Q2MGZjYjI0YWFmOTJmZTdhYTMxNDg4ZjRjN2UzNWYzMTQ3ZGMyNGZjZGFjZDNjMjhlZg";

    public function __construct()
    {
        parent::__construct();

        $accessToken = new OAuthToken();
        $accessToken->setAccessToken(self::ACCESS_TOKEN);
        $accessToken->setRefreshToken(self::REFRESH_TOKEN);
        $accessToken->setScope("edit");
        $accessToken->setValidToByLifetime(3600);

        $this->expectedResult = $accessToken;
    }

    /**
     * @test
     */
    public function normal()
    {
        $data = [
            "access_token" => self::ACCESS_TOKEN,
            "refresh_token" => self::REFRESH_TOKEN,
            "scope" => "edit",
            "expires_in" => 3600,
        ];

        $result = $this->helper->createOAuthTokenFromArray($data);

        $this->assertEquals($this->expectedResult->getAccessToken(), $result->getAccessToken());
        $this->assertEquals($this->expectedResult->getRefreshToken(), $result->getRefreshToken());
        $this->assertEquals($this->expectedResult->getScope(), $result->getScope());
        //Compare timestamps of the expected result and actual result with toleration of 2 seconds
        //When the test starts at the end of the second and the result is created in the very beginning of the other second, the test would fail without toleration
        $this->assertEquals($this->expectedResult->getValidTo()->getTimestamp(), $result->getValidTo()->getTimestamp(), "", 2);
    }


    /**
     * @test
     */
    public function missingFieldScope()
    {
        $data = [
            "access_token" => self::ACCESS_TOKEN,
            "refresh_token" => self::REFRESH_TOKEN,
            "expires_in" => 3600,
        ];

        $result = $this->helper->getUserInfoFromNecktieProfileResponse($data);

        $this->assertEquals(null, $result);
    }

    /**
     * @test
     */
    public function missingFieldAccessToken()
    {
        $data = [
            "refresh_token" => self::REFRESH_TOKEN,
            "scope" => "edit",
            "expires_in" => 3600,
        ];

        $result = $this->helper->getUserInfoFromNecktieProfileResponse($data);

        $this->assertEquals(null, $result);
    }

    /**
     * @test
     */
    public function missingFieldRefreshToken()
    {
        $data = [
            "access_token" => self::ACCESS_TOKEN,
            "scope" => "edit",
            "expires_in" => 3600,
        ];

        $result = $this->helper->createOAuthTokenFromArray($data);

        $this->assertEquals(null, $result);
    }


    /**
     * @test
     */
    public function missingFieldExpiresIn()
    {
        $data = [
            "access_token" => self::ACCESS_TOKEN,
            "refresh_token" => self::REFRESH_TOKEN,
            "scope" => "edit",
        ];

        $result = $this->helper->createOAuthTokenFromArray($data);

        $this->assertEquals(null, $result);
    }

    /**
     * @test
     */
    public function extraField()
    {
        $data = [
            "access_token" => self::ACCESS_TOKEN,
            "refresh_token" => self::REFRESH_TOKEN,
            "scope" => "edit",
            "expires_in" => 3600,
            "expires_in2" => 3600,
        ];

        $result = $this->helper->createOAuthTokenFromArray($data);

        $this->assertEquals($this->expectedResult->getAccessToken(), $result->getAccessToken());
        $this->assertEquals($this->expectedResult->getRefreshToken(), $result->getRefreshToken());
        $this->assertEquals($this->expectedResult->getScope(), $result->getScope());
        $this->assertEquals($this->expectedResult->getValidTo()->getTimestamp(), $result->getValidTo()->getTimestamp(), "", 2);
    }
}