<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 24.01.16
 * Time: 18:21
 */

namespace AppBundle\Tests\Services\NecktieGateway;


abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $entityManagerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $connectorMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $routerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $helperMock;

    public function __construct()
    {
        parent::__construct();

    }

    public function setUp()
    {
        $this->containerMock = $this
            ->getMockBuilder("Symfony\\Component\\DependencyInjection\\ContainerInterface")
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock = $this
            ->getMockBuilder("\\Doctrine\\Common\\Persistence\\ObjectManager")
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectorMock = $this
            ->getMockBuilder("\\AppBundle\\Services\\Connector")
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this
            ->getMockBuilder("Symfony\\Bundle\\FrameworkBundle\\Routing\\Router")
            ->disableOriginalConstructor()
            ->getMock();

        $this->helperMock = $this
            ->getMockBuilder("AppBundle\\Services\\NecktieGatewayHelper")
            ->disableOriginalConstructor()
            ->getMock();

        $this->helperMock
            ->method("isResponseOk")
            ->willReturn(true);
    }
}