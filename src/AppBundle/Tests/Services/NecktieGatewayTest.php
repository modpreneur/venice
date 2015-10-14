<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 12.10.15
 * Time: 15:14
 */

namespace AppBundle\Tests\Services;


use AppBundle\Services\NecktieGateway;

class NecktieGatewayTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $entityManagerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $connectorMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $productAccessManagerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $tokenStorageMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $routerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $repositoryMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $userMock;

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

        $this->productAccessManagerMock = $this
            ->getMockBuilder("\\AppBundle\\Services\\ProductAccessManager")
            ->disableOriginalConstructor()
            ->getMock();

        $this->tokenStorageMock = $this
            ->getMockBuilder("Symfony\\Component\\Security\\Core\\Authentication\\Token\\Storage\\TokenStorage")
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this
            ->getMockBuilder("Symfony\\Bundle\\FrameworkBundle\\Routing\\Router")
            ->disableOriginalConstructor()
            ->getMock();

        $this->repositoryMock = $this
            ->getMockBuilder("\\Doctrine\\ORM\\EntityRepository")
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMock = $this
            ->getMockBuilder("AppBundle\\Entity\\User")
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetRedirectUrlToNecktieLogin()
    {
        $this->containerMock
            ->expects($this->exactly(3))
            ->method("getParameter")
            ->willReturn("www.necktie.com", "client_id_value", "client_secret_value");

        $this->routerMock
            ->expects($this->once())
            ->method("generate")
            ->with("core_login_response")
            ->willReturn("www.venice.com");

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock
        );

        $uri = $necktieGateway->getRedirectUrlToNecktieLogin();

        $this->assertStringStartsWith
        (
            "www.necktie.com/oauth/v2/auth?client_id=client_id_value&client_secret=client_secret_value&redirect_uri=www.venice.com&grant_type=trusted_authorization&state=",
            $uri
        );

        $cookie = $necktieGateway->getStateCookie();
        $this->assertInstanceOf("Symfony\\Component\\HttpFoundation\\Cookie", $cookie);
    }


    /**
     * successfully, existing user
     */
    public function testGetUserByAccessTokenSuccessfullyExistingUser()
    {
        $this->userMock = $this
            ->getMockBuilder("AppBundle\\Entity\\User")
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock
            ->expects($this->once())
            ->method("getParameter")
            ->willReturn("www.necktie.com");

        $this->connectorMock
            ->expects($this->once())
            ->method("get")
            ->with(
                "www.necktie.com/api/v1/profile",
                "accessTokenString"
            )
            ->willReturn('{"user":{"username":"user","email":"user@webvalley.cz","roles":["ROLE_USER"],"id":3,"groups":[]}}');

        $this->entityManagerMock->expects($this->once())
            ->method("getRepository")
            ->with(
                "AppBundle:User"
            )
            ->willreturn($this->repositoryMock);

        $this->repositoryMock
            ->expects($this->once())
            ->method("findOneBy")
            ->with(
                $this->isType("array")
            )
            ->willReturn($this->userMock);

        $this->entityManagerMock
            ->expects($this->never())
            ->method("persist");

        $this->entityManagerMock
            ->expects($this->never())
            ->method("flush");

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock
        );

        $user = $necktieGateway->getUserByAccessToken("accessTokenString", true);
        $this->assertInstanceOf("AppBundle\\Entity\\User", $user);
    }

    /**
     * successfully, not existing user, persisted
     */
    public function testGetUserByAccessTokenSuccessfullyCreateANewUser()
    {
        $this->containerMock
            ->expects($this->once())
            ->method("getParameter")
            ->willReturn("www.necktie.com");

        $this->connectorMock
            ->expects($this->once())
            ->method("get")
            ->with(
                "www.necktie.com/api/v1/profile",
                "accessTokenString"
            )
            ->willReturn('{"user":{"username":"user","email":"user@webvalley.cz","roles":["ROLE_USER"],"id":3,"groups":[]}}');

        $this->entityManagerMock
            ->expects($this->once())
            ->method("getRepository")
            ->with(
                "AppBundle:User"
            )
            ->willreturn($this->repositoryMock);

        $this->repositoryMock
            ->expects($this->once())
            ->method("findOneBy")
            ->with(
                $this->isType("array")
            )
            ->willReturn(null);

        $this->entityManagerMock
            ->expects($this->once())
            ->method("persist");

        $this->entityManagerMock
            ->expects($this->once())
            ->method("flush");

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock
        );

        $user = $necktieGateway->getUserByAccessToken("accessTokenString", true);

        $this->assertInstanceOf("AppBundle\\Entity\\User", $user);
    }

    public  function testUpdateProductAccesses()
    {
        $this->containerMock
            ->expects($this->once())
            ->method("getParameter")
            ->willReturn("www.necktie.com");

        $tokenMock = $this
            ->getMockBuilder("Symfony\\Component\\Security\\Core\\Authentication\\Token\\TokenInterface")
            ->disableOriginalConstructor()
            ->getMock();

        $productMock = $this
            ->getMockBuilder("AppBundle\\Entity\\Product\\Product")
            ->disableOriginalConstructor()
            ->getMock();

        $tokenMock->expects($this->once())
            ->method("getUser")
            ->willReturn($this->userMock);

        $this->tokenStorageMock
            ->expects($this->once())
            ->method("getToken")
            ->willReturn($tokenMock);

        $this->connectorMock
            ->expects($this->once())
            ->method("get")
            ->with(
                "www.necktie.com/api/v1/product-accesses",
                "accessTokenString"
            )
            ->willReturn('{"productAccesses":[{"id":3,"product":6,"from_date":"2015-10-10T00:00:00+0200","to_date":"2018-01-13T00:00:00+0100","lifetime":false,"created_at":"2015-10-10T09:54:37+0200","updated_at":"2015-10-14T09:14:30+0200"},{"id":1,"product":1,"from_date":"2015-10-09T00:00:00+0200","lifetime":true,"created_at":"2015-10-09T19:21:42+0200","updated_at":"2015-10-14T09:14:30+0200"},{"id":2,"product":2,"from_date":"2015-10-09T00:00:00+0200","lifetime":true,"created_at":"2015-10-09T19:22:50+0200","updated_at":"2015-10-14T09:14:30+0200"}]}');

        $this->entityManagerMock
            ->expects($this->atLeastOnce())
            ->method("getRepository")
            ->with(
                "AppBundle:Product\\Product"
            )
            ->willreturn($this->repositoryMock);

        $this->repositoryMock
            ->expects($this->atLeastOnce())
            ->method("findOneBy")
            ->with(
                $this->isType("array")
            )
            ->willReturn($productMock);

        $this->productAccessManagerMock
            ->expects($this->atLeastOnce())
            ->method("giveAccessToProduct");

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock
        );

        $necktieGateway->updateProductAccesses("accessTokenString");
    }
}