<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.01.16
 * Time: 17:12
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


use AppBundle\Entity\Invoice;

class GetInvoicesFromNecktieResponseTest extends BaseTest
{
    public function __construct()
    {
        parent::__construct();

        $invoice = new Invoice();
        $invoice
            ->setId(1)
            ->setTotalPrice(133)
            ->setTransactionTime(new \DateTime("2016-01-24T17:21:01+0100"))
            ->setTransactionType("sale");

        $this->expectedResult = [$invoice];
    }


    /**
     * @test
     */
    public function normal()
    {
        $data = [
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
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEquals($this->expectedResult, $result);
    }


    /**
     * @test
     */
    public function extraField()
    {
        $data = [
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
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEquals($this->expectedResult, $result);
    }


    /**
     * @test
     */
    public function missingFields()
    {
        $data = [
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
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function missingFields2()
    {
        $data = [
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
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEmpty($result);
    }


    /**
     * @test
     */
    public function missingFields3()
    {
        $data = [
            "invoices" => [
                [
                    "total_customer_price" => 133,
                    "transaction_time" => "2016-01-24T17:21:01+0100",
                    "transaction_type" => "sale",
                    "transaction_type2" => "sa2le",
                ]
            ]
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function notArray()
    {
        $data = [
            "invoices" => 3
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEmpty($result);
    }


    /**
     * @test
     */
    public function notArray2()
    {
        $data = [
            "invoices" => [

            ]
        ];

        $result = $this->helper->getInvoicesFromNecktieResponse($data);

        $this->assertEmpty($result);
    }


}