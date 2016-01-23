<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 23.01.16
 * Time: 17:11
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


class HasErrorResponseTest extends BaseTest
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
        $data = '{"error":"some error"}';

        $result = $this->helper->hasError($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function invalidArray()
    {
        $data = ["error" => "some error"];

        $result = $this->helper->hasError($data);

        $this->assertEquals(true, $result);
    }


    /**
     * @test
     */
    public function validString()
    {
        $data = "valid response";

        $result = $this->helper->hasError($data);

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

        $result = $this->helper->hasError($data);

        $this->assertEquals(false, $result);
    }
}