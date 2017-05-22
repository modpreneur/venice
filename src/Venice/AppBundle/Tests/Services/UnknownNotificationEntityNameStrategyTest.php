<?php

namespace Venice\AppBundle\Tests\Services;

use Doctrine\ORM\EntityManagerInterface;
use Trinity\NotificationBundle\Entity\Notification;
use Venice\AppBundle\Entity\BillingPlan;
use Venice\AppBundle\Entity\Product\StandardProduct;
use Venice\AppBundle\Services\EntityOverrideHandler;
use Venice\AppBundle\Services\UnknownNotificationEntityNameStrategy;
use Venice\AppBundle\Tests\BaseTest;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class UnknownNotificationEntityNameStrategyTest
 */
class UnknownNotificationEntityNameStrategyTest extends BaseTest
{
    const DATA = '{"messageType":"notification","uid":"5825d05d347874.25020280","clientId":"3","createdAt":1478873181,"hash":"4be7798e2b490c0562b85a219e5bed350010d818f2fcff6c79811f46a8d42dcb","data":"[{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":1,\"product\":1,\"paySystemVendor\":1,\"defaultBillingPlan\":1},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d347a16.78768061\",\"entityId\":1},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":2,\"product\":2,\"paySystemVendor\":1,\"defaultBillingPlan\":2},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d3a1fb1.26594297\",\"entityId\":2},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":3,\"product\":3,\"paySystemVendor\":1,\"defaultBillingPlan\":3},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d3ff654.41022062\",\"entityId\":3},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":4,\"product\":4,\"paySystemVendor\":1,\"defaultBillingPlan\":4},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d4478c6.37638447\",\"entityId\":4},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":5,\"product\":5,\"paySystemVendor\":1,\"defaultBillingPlan\":5},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d4c5836.39336819\",\"entityId\":5},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":6,\"product\":6,\"paySystemVendor\":1,\"defaultBillingPlan\":6},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d4fea35.29856518\",\"entityId\":6},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":7,\"product\":7,\"paySystemVendor\":1,\"defaultBillingPlan\":7},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d565ba4.72383133\",\"entityId\":7},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":8,\"product\":8,\"paySystemVendor\":1,\"defaultBillingPlan\":8},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d5d06f5.96460717\",\"entityId\":8},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":9,\"product\":9,\"paySystemVendor\":1,\"defaultBillingPlan\":9},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d61c6a0.09835852\",\"entityId\":9},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":10,\"product\":10,\"paySystemVendor\":1,\"defaultBillingPlan\":10},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d68d2e1.81856757\",\"entityId\":10},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":11,\"product\":11,\"paySystemVendor\":1,\"defaultBillingPlan\":11},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d7190f4.64585086\",\"entityId\":11},{\"messageId\":\"5825d05d347874.25020280\",\"method\":\"PUT\",\"data\":{\"id\":12,\"product\":11,\"paySystemVendor\":2,\"defaultBillingPlan\":12},\"changeSet\":[],\"isForced\":true,\"createdAt\":1478873181,\"clientId\":\"3\",\"entityName\":\"default-billing-plans\",\"uid\":\"5825d05d74fd35.93301305\",\"entityId\":12}]","parent":"","sender":"necktie","destination":"client_3","user":"2"}';

    const PRODUCT_ID = 456;
    const VENDOR_ID = 789;
    const BILLING_PLAN_ID = 2048;

    const VALID_NOTIFICATION_DATA = [
        'id' => 123,
        'product' => self::PRODUCT_ID,
        'paySystemVendor' => self::VENDOR_ID,
        'defaultBillingPlan' => self::BILLING_PLAN_ID,
    ];

    /** @var  UnknownNotificationEntityNameStrategy */
    protected $strategy;

    /** @var  Notification */
    protected $notification;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $productRepositoryMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $billingPlanRepositoryMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $entityManagerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $billingPlanMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $entityOverrideHandlerMock;

    public function setUp()
    {
        $this->notification = new Notification();
        $this->entityManagerMock = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $this->productMock = $this->getMockBuilder(StandardProduct::class)->getMock();
        $this->billingPlanMock = $this->getMockBuilder(BillingPlan::class)->getMock();
        $this->productRepositoryMock = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $this->billingPlanRepositoryMock = $this->getMockBuilder(ObjectRepository::class)->disableOriginalConstructor()->getMock();
        $this->entityOverrideHandlerMock = $this->getMockBuilder(EntityOverrideHandler::class)->disableOriginalConstructor()->getMock();

        $this->strategy = new UnknownNotificationEntityNameStrategy($this->entityManagerMock, $this->entityOverrideHandlerMock);
    }

    public function testInvalidEntityName()
    {
        $this->notification->setEntityName('INVALID');
        $this->assertFalse($this->strategy->unknownEntityName($this->notification));
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @dataProvider testMissingDataFieldsProvider
     */
    public function testMissingDataFields($data)
    {
        $this->notification->setEntityName('default-billing-plans');
        $this->notification->setData($data);

        $this->assertTrue($this->strategy->unknownEntityName($this->notification));
    }

    /**
     */
    public function testItWorks()
    {
        $this->setMocks();

        $this->notification->setEntityName('default-billing-plans');
        $this->notification->setData(self::VALID_NOTIFICATION_DATA);

        $this->assertTrue($this->strategy->unknownEntityName($this->notification));
    }

    public function testMissingDataFieldsProvider()
    {
        return [
            [[]],
            [['id']],
            [['id', 'product']],
            [['id', 'product', 'paySystemVendor']],
            [['id', 'product', 'paySystemVendor']],
            [['gibb', 'e', 'rish']],
        ];
    }

    protected function setMocks()
    {
        $this->entityManagerMock->expects($this->exactly(2))->method('getRepository')
            ->withConsecutive([StandardProduct::class], [BillingPlan::class])
            ->willReturnOnConsecutiveCalls($this->productRepositoryMock, $this->billingPlanRepositoryMock);

        $this->productRepositoryMock->expects($this->once())->method('find')
            ->with(self::PRODUCT_ID)
            ->willReturn($this->productMock);

        $this->billingPlanRepositoryMock->expects($this->once())->method('find')
            ->with(self::BILLING_PLAN_ID)
            ->willReturn($this->billingPlanMock);

        $this->entityOverrideHandlerMock->expects($this->exactly(2))->method('getEntityClass')
            ->withConsecutive([StandardProduct::class], [BillingPlan::class])
            ->willReturnOnConsecutiveCalls(StandardProduct::class, BillingPlan::class);
    }

}