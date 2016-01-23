<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 15:57
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


class IsAccessTokenInvalidResponseTest extends BaseTest
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
        $data = $helper::NECKTIE_INVALID_ACCESS_TOKEN_ERROR;

        $result = $this->helper->isAccessTokenInvalidResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function invalidArray()
    {
        $helper = $this->helper;
        $data = json_decode($helper::NECKTIE_INVALID_ACCESS_TOKEN_ERROR, true);

        $result = $this->helper->isAccessTokenInvalidResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function validString()
    {
        $data = "valid response";

        $result = $this->helper->isAccessTokenInvalidResponse($data);

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

        $result = $this->helper->isAccessTokenInvalidResponse($data);

        $this->assertEquals(false, $result);
    }
}