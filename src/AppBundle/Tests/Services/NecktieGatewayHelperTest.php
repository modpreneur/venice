<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.01.16
 * Time: 20:04
 */

namespace AppBundle\Tests\Services;


use AppBundle\Entity\Invoice;
use AppBundle\Entity\OAuthToken;
use AppBundle\Services\NecktieGatewayHelper;

class NecktieGatewayHelperTest extends \PHPUnit_Framework_TestCase
{
    const ACCESS_TOKEN = "ZTcxZDNmYjU1ZTQ2NWExYmQ0YTRmMDBiZGJmZDEyOTBkNzhiYWQyNWQxM2UyZTMzMjQwMzY5OGRlODVhNzAwNw";
    const REFRESH_TOKEN = "ODBmZGRlZDUwYjRjY2Q2MGZjYjI0YWFmOTJmZTdhYTMxNDg4ZjRjN2UzNWYzMTQ3ZGMyNGZjZGFjZDNjMjhlZg";

    /** @var  NecktieGatewayHelper */
    protected $helper;

    public function setUp()
    {
        $this->helper = new NecktieGatewayHelper();
    }

    /**
     * @dataProvider providerTestCreateOAuthTokenFromArray
     *
     * @param $input
     * @param $expectedOutput
     */
    public function testCreateOAuthTokenFromArray($input, $expectedOutput)
    {
        $result = $this->helper->createOAuthTokenFromArray($input);

        if (is_object($expectedOutput)) {
            $this->assertEquals($expectedOutput->getAccessToken(), $result->getAccessToken());
            $this->assertEquals($expectedOutput->getRefreshToken(), $result->getRefreshToken());
            $this->assertEquals($expectedOutput->getScope(), $result->getScope());
            $this->assertEquals($expectedOutput->getValidTo()->getTimestamp(), $result->getValidTo()->getTimestamp(), "", 2);
        } else {
            $this->assertEquals($expectedOutput, $result);
        }


    }

    /**
     * @dataProvider providerTestGetInvoicesFromNecktieResponse
     */
    public function testGetInvoicesFromNecktieResponse($input, $expectedOutput)
    {
        $result = $this->helper->getInvoicesFromNecktieResponse($input);

        $this->assertEquals($expectedOutput, $result);
    }

    public function providerTestCreateOAuthTokenFromArray()
    {
        $accessToken = new OAuthToken();
        $accessToken->setAccessToken(self::ACCESS_TOKEN);
        $accessToken->setRefreshToken(self::REFRESH_TOKEN);
        $accessToken->setScope("edit");
        $accessToken->setValidToByLifetime(3600);

        return [
            //#0 test data
            [

                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "refresh_token" => self::REFRESH_TOKEN,
                    "scope" => "edit",
                    "expires_in" => 3600,
                ],
                //expected result
                $accessToken
            ],
            //#1 test data
            [
                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "refresh_token" => self::REFRESH_TOKEN,
                    "scope" => "edit",
                    "expires_in" => 3600,
                    "expires_in2" => 3600,
                ],
                //expected result
                $accessToken
            ],
            //#2 test data
            [
                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "refresh_token" => self::REFRESH_TOKEN,
                    "expires_in" => 3600,
                ],
                //expected result
                null
            ],
            //#3 test data
            [
                //input data
                [
                    "refresh_token" => self::REFRESH_TOKEN,
                    "scope" => "edit",
                    "expires_in" => 3600,
                ],
                //expected result
                null
            ],
            //#4 test data
            [
                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "scope" => "edit",
                    "expires_in" => 3600,
                ],
                //expected result
                null
            ],
            //#5 test data
            [
                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "refresh_token" => self::REFRESH_TOKEN,
                    "scope" => "edit",
                ],
                //expected result
                null
            ]
            ,
            //#6 test data
            [
                //input data
                [
                    "access_token" => self::ACCESS_TOKEN,
                    "refresh_token" => self::REFRESH_TOKEN,
                    "scope" => "edit",
                    "expires_in" => 3600,
                    "expires_in2" => 3600,
                ],
                //expected result
                $accessToken
            ],
        ];
    }

    public function providerTestGetInvoicesFromNecktieResponse()
    {
        $invoice = new Invoice();
        $invoice
            ->setId(1)
            ->setTotalPrice(133)
            ->setTransactionTime(new \DateTime("2016-01-24T17:21:01+0100"))
            ->setTransactionType("sale");

        return [
            //#0 test data - normal data
            [
                //input data
                [
                    "invoices" => [
                        [
                            "id" => 1,
                            "total_customer_price" => 133,
                            "transaction_time" => "2016-01-24T17:21:01+0100",
                            "transaction_type" => "sale",
                            "items" => [
                                "product" => [
                                    "name" => "productName"
                                ]
                            ]
                        ]
                    ]
                ],
                //expected result
                [$invoice]
            ],
            //#1 test data - extra field
            [
                //input data
                [
                    "invoices" => [
                        [
                            "id" => 1,
                            "total_customer_price" => 133,
                            "transaction_time" => "2016-01-24T17:21:01+0100",
                            "transaction_type" => "sale",
                            "transaction_type2" => "sa2le",
                            "items" => [
                                "product" => [
                                    "name" => "productName"
                                ]
                            ]
                        ]
                    ]
                ],
                //expected result
                [$invoice]
            ],
            //#2 test data - missing field
            [
                //input data
                [
                    "invoices" => [
                        [
                            "total_customer_price" => 133,
                            "transaction_time" => "2016-01-24T17:21:01+0100",
                            "transaction_type" => "sale",
                            "transaction_type2" => "sa2le",
                            "items" => [
                                "product" => [
                                    "name" => "productName"
                                ]
                            ]
                        ]
                    ]
                ],
                //expected result
                []
            ],
            //#3 test data - missing field
            [
                //input data
                [
                    "invoices" => [
                        [
                            "total_customer_price" => 133,
                            "transaction_time" => "2016-01-24T17:21:01+0100",
                            "transaction_type2" => "sa2le",
                            "items" => [
                                "product" => [
                                    "name" => "productName"
                                ]
                            ]
                        ]
                    ]
                ],
                //expected result
                []
            ],
            //#4 test data - missing field
            [
                //input data
                [
                    "invoices" => [
                        [
                            "total_customer_price" => 133,
                            "transaction_time" => "2016-01-24T17:21:01+0100",
                            "transaction_type" => "sale",
                            "transaction_type2" => "sa2le",
                        ]
                    ]
                ],
                //expected result
                []
            ],
            //#5 test data - not array
            [
                //input data
                [
                    "invoices" => 3
                ],
                //expected result
                []
            ],
            //#6 test data - not array
            [
                //input data
                [
                    "invoices" => [

                    ]
                ],
                //expected result
                []
            ],

        ];
    }
}