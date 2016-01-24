<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 16:27
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


class GetUserInfoFromNecktieProfileResponseTest extends BaseTest
{
    public function __construct()
    {
        parent::__construct();

        $this->expectedResult = [
            "username" => "superAdmin",
            "email" => "superAdmin@webvalley.cz",
            "id" => 2
        ];
    }

    /**
     * @test
     */
    public function normal()
    {
        $data = [
            "user" => [
                "username" => "superAdmin",
                "email" => "superAdmin@webvalley.cz",
                "id" => 2
            ]
        ];

        $result = $this->helper->getUserInfoFromNecktieProfileResponse($data);

        $this->assertEquals($this->expectedResult, $result);
    }

    /**
     * @test
     */
    public function extraFields()
    {
        $data = [
            "user" => [
                "username" => "superAdmin",
                "email" => "superAdmin@webvalley.cz",
                "id" => 2,
                "extra field" => "field",
                "extra field2" => "field2",
            ]
        ];

        $result = $this->helper->getUserInfoFromNecktieProfileResponse($data);

        $this->assertEquals($this->expectedResult, $result);
    }

    /**
     * @test
     */
    public function missingFields()
    {
        $data = [
            "user" => [
                "username" => "superAdmin",
                "email" => "superAdmin@webvalley.cz",
            ]
        ];

        $result = $this->helper->getUserInfoFromNecktieProfileResponse($data);

        $this->assertEquals(null, $result);
    }
}
