<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.01.16
 * Time: 20:04.
 */
namespace Venice\AppBundle\Tests\Services;

use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\OAuthToken;
use Venice\AppBundle\Services\NecktieGatewayHelper;
use Venice\AppBundle\Tests\BaseTest;

class NecktieGatewayHelperTest extends BaseTest
{
    const ACCESS_TOKEN = 'ZTcxZDNmYjU1ZTQ2NWExYmQ0YTRmMDBiZGJmZDEyOTBkNzhiYWQyNWQxM2UyZTMzMjQwMzY5OGRlODVhNzAwNw';
    const REFRESH_TOKEN = 'ODBmZGRlZDUwYjRjY2Q2MGZjYjI0YWFmOTJmZTdhYTMxNDg4ZjRjN2UzNWYzMTQ3ZGMyNGZjZGFjZDNjMjhlZg';

    /** @var  NecktieGatewayHelper */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->helper = new NecktieGatewayHelper();
    }

    /**
     * @dataProvider providerTestCreateOAuthTokenFromArray
     */
    public function testCreateOAuthTokenFromArray($input, $expectedOutput)
    {
        $result = $this->helper->createOAuthTokenFromArray($input);

        if (is_object($expectedOutput)) {
            $this->assertEquals($expectedOutput->getAccessToken(), $result->getAccessToken());
            $this->assertEquals($expectedOutput->getRefreshToken(), $result->getRefreshToken());
            $this->assertEquals($expectedOutput->getScope(), $result->getScope());
            $this->assertDatesTimeEquals($expectedOutput->getValidTo(), $result->getValidTo(), '', 20);
        } else {
            $this->assertEquals($expectedOutput, $result);
        }
    }

    /**
     * @dataProvider providerTestGetInvoicesFromNecktieResponse
     */
    public function testGetInvoicesFromNecktieResponse($input, $expectedOutput)
    {
        $result = $this->helper->getOrdersFromNecktieResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestGetUserInfoFromNecktieProfileResponse
     */
    public function testGetUserInfoFromNecktieProfileResponse($input, $expectedOutput)
    {
        $result = $this->helper->getUserInfoFromNecktieProfileResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestHasError
     */
    public function testHasError($input, $expectedOutput)
    {
        $result = $this->helper->hasError($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestIsAccessTokenExpiredResponse
     */
    public function testIsAccessTokenExpiredResponse($input, $expectedOutput)
    {
        $result = $this->helper->isAccessTokenExpiredResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestIsAccessTokenInvalidResponse
     */
    public function testIsAccessTokenInvalidResponse($input, $expectedOutput)
    {
        $result = $this->helper->isAccessTokenInvalidResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestIsInvalidClientResponse
     */
    public function testIsInvalidClientResponse($input, $expectedOutput)
    {
        $result = $this->helper->isInvalidClientResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    /**
     * @dataProvider providerTestIsRefreshTokenExpiredResponse
     */
    public function testIsRefreshTokenExpiredResponse($input, $expectedOutput)
    {
        $result = $this->helper->isRefreshTokenExpiredResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    public function testIsResponseOkCallsAllMethods()
    {
        $helperMock = $this->getMockBuilder("Venice\AppBundle\\Services\\NecktieGatewayHelper")
            // Mock only those methods
            ->setMethods(
                [
                    'isAccessTokenExpiredResponse',
                    'isAccessTokenInvalidResponse',
                    'isRefreshTokenExpiredResponse',
                    'isInvalidClientResponse',
                    'hasError',
                ]
            )
            ->getMock();

        $helperMock->expects($this->once())
            ->method('isAccessTokenExpiredResponse')
            ->will($this->returnValue(false));

        $helperMock->expects($this->once())
            ->method('isAccessTokenInvalidResponse')
            ->will($this->returnValue(false));

        $helperMock->expects($this->once())
            ->method('isRefreshTokenExpiredResponse')
            ->will($this->returnValue(false));

        $helperMock->expects($this->once())
            ->method('isInvalidClientResponse')
            ->will($this->returnValue(false));

        $helperMock->expects($this->once())
            ->method('hasError')
            ->will($this->returnValue(false));

        $this->assertTrue($helperMock->isResponseOk('response'));
    }

    /*
     * ############# DATA PROVIDERS #############
     */

    public function providerTestCreateOAuthTokenFromArray()
    {
        $accessToken = new OAuthToken();
        $accessToken->setAccessToken(self::ACCESS_TOKEN);
        $accessToken->setRefreshToken(self::REFRESH_TOKEN);
        $accessToken->setScope('edit');
        $accessToken->setValidToByLifetime(3600);

        return [
            //#0 test data
            [

                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'refresh_token' => self::REFRESH_TOKEN,
                    'scope' => 'edit',
                    'expires_in' => 3600,
                ],
                //expected result
                $accessToken,
            ],
            //#1 test data
            [
                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'refresh_token' => self::REFRESH_TOKEN,
                    'scope' => 'edit',
                    'expires_in' => 3600,
                    'expires_in2' => 3600,
                ],
                //expected result
                $accessToken,
            ],
            //#2 test data
            [
                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'refresh_token' => self::REFRESH_TOKEN,
                    'expires_in' => 3600,
                ],
                //expected result
                null,
            ],
            //#3 test data
            [
                //input data
                [
                    'refresh_token' => self::REFRESH_TOKEN,
                    'scope' => 'edit',
                    'expires_in' => 3600,
                ],
                //expected result
                null,
            ],
            //#4 test data
            [
                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'scope' => 'edit',
                    'expires_in' => 3600,
                ],
                //expected result
                null,
            ],
            //#5 test data
            [
                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'refresh_token' => self::REFRESH_TOKEN,
                    'scope' => 'edit',
                ],
                //expected result
                null,
            ],
            //#6 test data
            [
                //input data
                [
                    'access_token' => self::ACCESS_TOKEN,
                    'refresh_token' => self::REFRESH_TOKEN,
                    'scope' => 'edit',
                    'expires_in' => 3600,
                    'expires_in2' => 3600,
                ],
                //expected result
                $accessToken,
            ],
        ];
    }

    public function providerTestGetInvoicesFromNecktieResponse()
    {
        $invoice = new Order();
        $invoice
            ->setId(1)
            ->setTotalPrice(133)
            ->setTransactionTime(new \DateTime('2016-01-24T17:21:01+0100'))
            ->setTransactionType('sale');

        return [
            //#0 test data - normal data
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [$invoice],
            ],
            //#1 test data - extra field
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                            'transaction_type2' => 'sa2le',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [$invoice],
            ],
            //#2 test data - missing field id
            [
                //input data
                [
                    'invoices' => [
                        [
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#3 test data - missing field total_customer_price
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#4 test data - missing field
            [
                //input data
                [
                    'invoices' => [
                        [
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                            'transaction_type2' => 'sa2le',
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#5 test data - missing field transaction_time
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'total_customer_price' => 133,
                            'transaction_type' => 'sale',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#6 test data - missing field transaction_type
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'items' => [
                                'product' => [
                                    'name' => 'productName',
                                ],
                            ],
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#7 test data - missing field items
            [
                //input data
                [
                    'invoices' => [
                        [
                            'id' => 1,
                            'total_customer_price' => 133,
                            'transaction_time' => '2016-01-24T17:21:01+0100',
                            'transaction_type' => 'sale',
                        ],
                    ],
                ],
                //expected result
                [],
            ],
            //#8 test data - not array
            [
                //input data
                [
                    'invoices' => 3,
                ],
                //expected result
                [],
            ],
            //#9 test data - not array
            [
                //input data
                [
                    'invoices' => [

                    ],
                ],
                //expected result
                [],
            ],

        ];
    }

    public function providerTestGetUserInfoFromNecktieProfileResponse()
    {
        $userInfo = [
            'id' => 2,
            'username' => 'superAdmin',
            'email' => 'superAdmin@webvalley.cz',
        ];

        return [
            //#0 test data - normal data
            [
                //input data
                [
                    'user' => [
                        'username' => 'superAdmin',
                        'email' => 'superAdmin@webvalley.cz',
                        'id' => 2,
                    ],
                ],
                //expected result
                $userInfo,
            ],
            //#1 test data - extra field
            [
                //input data
                [
                    'user' => [
                        'username' => 'superAdmin',
                        'email' => 'superAdmin@webvalley.cz',
                        'id' => 2,
                        'extra field' => 'field',
                        'extra field2' => 'field2',
                    ],
                ],
                //expected result
                $userInfo,
            ],
            //#2 test data - missing field
            [
                //input data
                [
                    'user' => [
                        'username' => 'superAdmin',
                        'email' => 'superAdmin@webvalley.cz',
                    ],
                ],
                //expected result
                null,
            ],
            //#2 test data - not array
            [
                //input data
                [
                    'not array',
                ],
                //expected result
                null,
            ],

        ];
    }

    public function providerTestHasError()
    {
        return [
            //#0 test data - json error
            [
                //input data
                '{"error":"some error"}',
                //expected result
                true,
            ],
            //#1 test data - array error
            [
                //input data
                [
                    'error' => 'some error',
                ],
                //expected result
                true,
            ],
            //#2 test data - missing field
            [
                //input data
                [
                    'user' => [
                        'username' => 'superAdmin',
                        'email' => 'superAdmin@webvalley.cz',
                    ],
                ],
                //expected result
                null,
            ],
            //#3 test data - valid string
            [
                //input data
                [
                    'valid response',
                ],
                //expected result
                false,
            ],
            //#4 test data - valid array
            [
                //input data
                [
                    [
                        'response' => 'valid!',
                    ],
                ],
                //expected result
                false,
            ],

        ];
    }

    public function providerTestIsAccessTokenExpiredResponse()
    {
        return [
            //#0 test data - string error
            [
                //input data
                '{"error":"invalid_grant","error_description":"The access token provided has expired."}',
                //expected result
                true,
            ],
            //#1 test data - array error
            [
                //input data
                json_decode('{"error":"invalid_grant","error_description":"The access token provided has expired."}', true),
                //expected result
                true,
            ],
            //#2 test data - valid string
            [
                //input data
                'valid response',
                //expected result
                false,
            ],
            //#3 test data - valid string
            [
                //input data
                [
                    'response' => 'valid!',
                ],
                //expected result
                false,
            ],
        ];
    }

    public function providerTestIsAccessTokenInvalidResponse()
    {
        return [
            //#0 test data - string error
            [
                //input data
                '{"error":"invalid_grant","error_description":"The access token provided is invalid."}',
                //expected result
                true,
            ],
            //#1 test data - array error
            [
                //input data
                json_decode('{"error":"invalid_grant","error_description":"The access token provided is invalid."}', true),
                //expected result
                true,
            ],
            //#2 test data - valid string
            [
                //input data
                'valid response',
                //expected result
                false,
            ],
            //#3 test data - valid string
            [
                //input data
                [
                    'response' => 'valid!',
                ],
                //expected result
                false,
            ],
        ];
    }

    public function providerTestIsInvalidClientResponse()
    {
        return [
            //#0 test data - string error
            [
                //input data
                '{"error":"invalid_token","error_description":"The client credentials are invalid"}',
                //expected result
                true,
            ],
            //#1 test data - array error
            [
                //input data
                json_decode('{"error":"invalid_token","error_description":"The client credentials are invalid"}', true),
                //expected result
                true,
            ],
            //#2 test data - valid string
            [
                //input data
                'valid response',
                //expected result
                false,
            ],
            //#3 test data - valid string
            [
                //input data
                [
                    'response' => 'valid!',
                ],
                //expected result
                false,
            ],
        ];
    }

    public function providerTestIsRefreshTokenExpiredResponse()
    {
        return [
            //#0 test data - string error
            [
                //input data
                '{"error":"invalid_grant","error_description":"Invalid refresh token"}',
                //expected result
                true,
            ],
            //#1 test data - array error
            [
                //input data
                json_decode('{"error":"invalid_grant","error_description":"Invalid refresh token"}', true),
                //expected result
                true,
            ],
            //#2 test data - valid string
            [
                //input data
                'valid response',
                //expected result
                false,
            ],
            //#3 test data - valid string
            [
                //input data
                [
                    'response' => 'valid!',
                ],
                //expected result
                false,
            ],
        ];
    }
}
