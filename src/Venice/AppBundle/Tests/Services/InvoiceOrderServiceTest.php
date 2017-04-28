<?php

namespace Venice\AppBundle\Tests\Services;

use Venice\AppBundle\Entity\Order;
use Venice\AppBundle\Entity\OrderItem;
use Venice\AppBundle\Services\InvoiceOrderService;
use Venice\AppBundle\Tests\BaseTest;


/**
 * Class InvoiceOrderServiceTest
 */
class InvoiceOrderServiceTest extends BaseTest
{
    /**
     * @var InvoiceOrderService
     */
    protected $service;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->service = new InvoiceOrderService();
    }

    public function testGetZeroWhenThereAreNoOrders()
    {
        self::assertEquals(0, $this->service->getInvoiceOrderForProductName([], 'productA'));
    }

    public function testGetZeroWhenThereIsNoOrderWithTheProduct()
    {
        $order = new Order();
        $order->setFirstPaymentDate(new \DateTime('2016-10-21'));
        $item1 = new OrderItem();
        $item2 = new OrderItem();
        $item1->setProductName('ProductA');
        $item2->setProductName('ProductB');

        self::assertEquals(0, $this->service->getInvoiceOrderForProductName([], 'productC'));
    }

    public function testReturnsValidNumbersForOneOrder()
    {
        $order = new Order();
        $order->setFirstPaymentDate(new \DateTime('2016-10-21'));
        $item1 = new OrderItem();
        $item2 = new OrderItem();
        $item3 = new OrderItem();
        $item1->setProductName('ProductA');
        $item2->setProductName('ProductB');
        $item3->setProductName('ProductC');

        $order->addItem($item1);
        $order->addItem($item2);
        $order->addItem($item3);

        self::assertEquals(1, $this->service->getInvoiceOrderForProductName([$order], $item1->getProductName()));
        self::assertEquals(2, $this->service->getInvoiceOrderForProductName([$order], $item2->getProductName()));
        self::assertEquals(3, $this->service->getInvoiceOrderForProductName([$order], $item3->getProductName()));
    }

    public function testReturnsValidNumbersForTwoOrders()
    {
        $order1 = new Order();
        $order1->setFirstPaymentDate(new \DateTime('2016-10-21'));
        $item1 = new OrderItem();
        $item1->setProductName('ProductA');
        $order1->addItem($item1);

        $order2 = new Order();
        $order2->setFirstPaymentDate(new \DateTime('2016-10-23'));
        $item2 = new OrderItem();
        $item2->setProductName('ProductB');
        $order2->addItem($item2);

        $item3 = new OrderItem();
        $item3->setProductName('ProductC');
        $order2->addItem($item3);

        self::assertEquals(1, $this->service->getInvoiceOrderForProductName([$order1, $order2], $item1->getProductName()));
        self::assertEquals(2, $this->service->getInvoiceOrderForProductName([$order1, $order2], $item2->getProductName()));
        self::assertEquals(3, $this->service->getInvoiceOrderForProductName([$order1, $order2], $item3->getProductName()));

    }
}