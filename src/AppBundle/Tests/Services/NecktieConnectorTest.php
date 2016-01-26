<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 25.01.16
 * Time: 14:44
 */

namespace AppBundle\Tests\Services;


use AppBundle\Entity\OAuthToken;
use AppBundle\Entity\User;
use AppBundle\Services\NecktieConnector;
use AppBundle\Tests\BaseTest;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

class NecktieConnectorTest extends BaseTest
{
    public function testGetAccessTokenFromUserWhichHasToken()
    {
        $connector = new NecktieConnector();

        $user = new User();
        $token = (new OAuthToken())->setAccessToken("accessToken");
        $user->addOAuthToken($token);
        $accessToken = null;

        $this->assertEquals("accessToken", $this->invokeMethod($connector, "getAccessToken", [$user, $accessToken]));
    }

    
    /**
     * @expectedException \Exception
     */
    public function testGetAccessTokenFromUserWhoHasNoToken()
    {
        $connector = new NecktieConnector();

        $user = new User();
        $accessToken = null;

        $this->assertEquals(null, $this->invokeMethod($connector, "getAccessToken", [$user, $accessToken]));
    }


    public function testGetAccessTokenFromUserWhoHasNoTokenButAccessTokenStringIsPresent()
    {
        $connector = new NecktieConnector();

        $user = new User();
        $accessToken = "accessTokenString";

        $this->assertEquals("accessTokenString", $this->invokeMethod($connector, "getAccessToken", [$user, $accessToken]));
    }


    /**
     * @dataProvider providerTestPrepareOptions
     */
    public function testPrepareOptions($input, $expectedOutput)
    {
        $connector = new NecktieConnector();

        $this->assertEquals($expectedOutput, $this->invokeMethod($connector, "prepareOptions", $input));
    }


    public function testSetBaseUri()
    {
        $connectorMock = $this->getMockBuilder("AppBundle\\Services\\NecktieConnector")
            //do not mock any method
            ->setMethods(null)
            ->getMock();


        $client = $connectorMock->getClient();
        $this->assertNull($client);

        $connectorMock->setBaseUri("http://192.168.99.100/app_dev.php");

        $client = $connectorMock->getClient();

        $this->assertEquals(new Client(["base_uri" => "http://192.168.99.100/app_dev.php"]), $client);


    }


    public function testCreateRequest()
    {
        $clientMock = $this->getMockBuilder("GuzzleHttp\\Client")
            ->getMock();

        $method = "get";
        $uri = "/api/user";
        $options = ["redirects" => false];

        $clientMock->expects($this->once())
            ->method("request")
            ->with($method, $uri, $options);

        $connector = new NecktieConnector();
        $connector->setClient($clientMock);

        $this->invokeMethod($connector, "createRequest", [$method, $uri, $options]);
    }


    /**
     * @dataProvider providerTestGetResponseWithoutException
     */
    public function testGetResponseWithoutException($input = [], $expectedOutput)
    {
        list($guzzleResponseMock, $user, $method, $uri, $data, $accessToken, $sendAsJson) = $input;

        $response = $this->getResponse($guzzleResponseMock, $user, $method, $uri, $data, $accessToken, $sendAsJson);

        $this->assertEquals($expectedOutput, $response);
    }

    /**
     * @dataProvider providerTestGetResponseWithException
     * @expectedException AppBundle\Exceptions\UnsuccessfulNecktieResponseException
     */
    public function testGetResponseWithServerException($input = [], $expectedOutput)
    {
        list($guzzleResponseMock, $user, $method, $uri, $data, $accessToken, $sendAsJson) = $input;

        $response = $this->getResponse($guzzleResponseMock, $user, $method, $uri, $data, $accessToken, $sendAsJson);

        $this->assertEquals($expectedOutput, $response);
    }


    protected function getResponse($guzzleResponseMock, $user, $method, $uri, $data, $accessToken, $sendAsJson)
    {
        $connector = new NecktieConnector();

        $connectorMock = $this->getMockBuilder("AppBundle\\Services\\NecktieConnector")
            ->setMethods(["getAccessToken", "prepareOptions"])
            ->getMock();

        $clientMock = new MockHandler([$guzzleResponseMock]);

        $container = [];
        $history = Middleware::history($container);
        $stack = HandlerStack::create($clientMock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);

        $connectorMock->setClient($client);

        $connectorMock->expects($this->once())
            ->method("getAccessToken")
            ->will($this->returnValue($accessToken));

        $connectorPreparedOptions = $this->invokeMethod(
            $connector,
            "prepareOptions",
            [$method, $data, $accessToken, $sendAsJson]
        );

        $connectorMock->expects($this->once())
            ->method("prepareOptions")
            ->will($this->returnValue($connectorPreparedOptions));


        $response = $connectorMock->getResponse($user, $method, $uri, $data, $accessToken, $sendAsJson);

        $transaction = $container[0];
        $this->assertEquals($method, $transaction["request"]->getMethod());
        $this->assertEquals($uri, $transaction["request"]->getUri()->getPath());

        return $response;
    }


    /*
    * ############# DATA PROVIDERS #############
    */


    public function providerTestPrepareOptions()
    {
        return [
            //#0 test data
            [
                //input data - get
                [
                    "get",
                    [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                    "accessTokenString",
                ],
                //expected result
                [
                    "headers" => [
                        "Authorization" => "Bearer accessTokenString"
                    ],
                    "query" => [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                ]
            ],
            //#1 test data - post
            [
                //input data
                [
                    "post",
                    [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                    "accessTokenString",
                ],
                //expected result
                [
                    "headers" => [
                        "Authorization" => "Bearer accessTokenString"
                    ],
                    "form_params" => [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                ]
            ],
            //#2 test data - post json
            [
                //input data
                [
                    "post",
                    [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                    "accessTokenString",
                    true
                ],
                //expected result
                [
                    "headers" => [
                        "Authorization" => "Bearer accessTokenString"
                    ],
                    "json" => [
                        "a" => "aaa",
                        "b" => "bbb",
                    ],
                ]
            ],
        ];
    }

    public function providerTestGetResponseWithoutException()
    {
        $user = new User();
        $token = (new OAuthToken())->setAccessToken("usersAccessToken");
        $user->addOAuthToken($token);

        $data = [
            "data1" => "value1",
            "data2" => "value2"
        ];
        $sendAsJson = false;


        return [
            //#0 test data
            [
                //input data - get
                [
                    new Response(200, [], "ok"),
                    $user,
                    "GET",
                    "/api/v1/test",
                    $data,
                    null,
                    $sendAsJson

                ],
                //expected result
                "ok"
            ],
            //#1 test data - ClientException, code 404 - returns null
            [
                //input data - get
                [
                    new Response(404, [], "not found"),
                    $user,
                    "GET",
                    "/api/v1/test",
                    $data,
                    null,
                    $sendAsJson

                ],
                //expected result
                null
            ],
        ];
    }

    public function providerTestGetResponseWithException()
    {
        $user = new User();
        $token = (new OAuthToken())->setAccessToken("usersAccessToken");
        $user->addOAuthToken($token);

        $data = [
            "data1" => "value1",
            "data2" => "value2"
        ];
        $sendAsJson = false;

        return [
            //#0 test data - server exception
            [
                //input data - get
                [
                    new Response(500, [], "error"),
                    $user,
                    "GET",
                    "/api/v1/test",
                    $data,
                    null,
                    $sendAsJson

                ],
                //expected result
                "error"
            ],

            //#0 test data - client exception
            [
                //input data - get
                [
                    new Response(400, [], "bad request"),
                    $user,
                    "GET",
                    "/api/v1/test",
                    $data,
                    null,
                    $sendAsJson

                ],
                //expected result
                "error"
            ],
        ];
    }

}