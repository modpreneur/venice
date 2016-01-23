<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 16:56
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


class IsAccessTokenExpiredResponseTest extends BaseTest
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @test
     */
    public function invalidString()
    {
        $helper = $this->helper;
        $data = $helper::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR;

        $result = $this->helper->isAccessTokenExpiredResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function invalidArray()
    {
        $helper = $this->helper;
        $data = json_decode($helper::NECKTIE_EXPIRED_ACCESS_TOKEN_ERROR, true);

        $result = $this->helper->isAccessTokenExpiredResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function validString()
    {
        $data = "valid response";

        $result = $this->helper->isAccessTokenExpiredResponse($data);

        $this->assertEquals(false, $result);
    }


    /**
     * @test
     */
    public function validArray()
    {
        $data = [
            "response" => "valid!"
        ];

        $result = $this->helper->isAccessTokenExpiredResponse($data);

        $this->assertEquals(false, $result);
    }
}