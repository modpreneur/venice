<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 17:09
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


class IsInvalidClientResponseTest extends BaseTest
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
        $data = $helper::NECKTIE_INVALID_CLIENT_ERROR;

        $result = $this->helper->isInvalidClientResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function invalidArray()
    {
        $helper = $this->helper;
        $data = json_decode($helper::NECKTIE_INVALID_CLIENT_ERROR, true);

        $result = $this->helper->isInvalidClientResponse($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function validString()
    {
        $data = "valid response";

        $result = $this->helper->isInvalidClientResponse($data);

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

        $result = $this->helper->isInvalidClientResponse($data);

        $this->assertEquals(false, $result);
    }
}