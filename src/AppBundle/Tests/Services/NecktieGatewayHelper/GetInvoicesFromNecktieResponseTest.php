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