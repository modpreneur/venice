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

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $necktieGatewayHelperMock;

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

        $this->userMock
            ->method("isLastAccessTokenValid")
            ->willReturn(true);


        $this->necktieGatewayHelperMock = $this
            ->getMockBuilder("AppBundle\\Services\\NecktieGatewayHelper")
            ->disableOriginalConstructor()
            ->getMock();

        $this->necktieGatewayHelperMock
            ->method("isResponseOk")
            ->willReturn(true);

    }

    public function testGetRedirectUrlToLogin()
    {
        $this->containerMock
            ->expects($this->exactly(4))
            ->method("getParameter")
            ->willReturn("www.necktie.com", "client_id_value", "client_secret_value", "necktie_login_response");

        $this->routerMock
            ->expects($this->once())
            ->method("generate")
            ->with("necktie_login_response")
            ->willReturn("www.venice.com");

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock,
            $this->necktieGatewayHelperMock
        );

        $uri = $necktieGateway->getRedirectUrlToLogin();

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

        $this->necktieGatewayHelperMock
            ->method("getUserInfoFromNecktieProfileResponse")
            ->willReturn(
                [
                    "username" => "user",
                    "email" => "user@mail.com",
                    "id" => 1
                ]
            );

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
            $this->routerMock,
            $this->necktieGatewayHelperMock
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

        $this->necktieGatewayHelperMock
            ->method("getUserInfoFromNecktieProfileResponse")
            ->willReturn(
                [
                    "username" => "user",
                    "email" => "user@mail.com",
                    "id" => 1
                ]
            );

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
            $this->routerMock,
            $this->necktieGatewayHelperMock
        );

        $user = $necktieGateway->getUserByAccessToken("accessTokenString", true);

        $this->assertInstanceOf("AppBundle\\Entity\\User", $user);
    }

    public function testUpdateProductAccesses()
    {
        $this->containerMock
            ->expects($this->atLeastOnce())
            ->method("getParameter")
            ->willReturn("www.necktie.com");

        $productMock = $this
            ->getMockBuilder("AppBundle\\Entity\\Product\\Product")
            ->disableOriginalConstructor()
            ->getMock();

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

        $this->userMock
            ->expects($this->once())
            ->method("getLastAccessToken")
            ->will($this->returnValue("accessTokenString"));

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock,
            $this->necktieGatewayHelperMock
        );

        $necktieGateway->updateProductAccesses($this->userMock);
    }

    public function testGetInvoices()
    {
        $this->containerMock
            ->expects($this->atLeastOnce())
            ->method("getParameter")
            ->willReturn("www.necktie.com");

        $this->userMock
            ->expects($this->once())
            ->method("getLastAccessToken")
            ->willReturn("accessTokenString");

        $this->connectorMock
            ->expects($this->once())
            ->method("get")
            ->with(
                "www.necktie.com/api/v1/invoices",
                "accessTokenString"
            )
            ->willReturn('{"invoices":[{"id":1,"pay_system":{"id":1,"name":"Clickbank","vendor":[{"id":1,"name":"metest","secretKey":"C38BBALA90A9AF4C","apiKey":"API-NAG1OMPUO3EELVUHNOHA310NOGTD6U8N","devApiKey":"DEV-LC2U7HN8FEVC4USNERCM3N0D013A9DU2"}],"postback":true},"pay_system_vendor":{"id":1,"pay_system":{"id":1,"name":"Clickbank","vendor":[],"postback":true},"name":"metest","secretKey":"C38BBALA90A9AF4C","apiKey":"API-NAG1OMPUO3EELVUHNOHA310NOGTD6U8N","devApiKey":"DEV-LC2U7HN8FEVC4USNERCM3N0D013A9DU2"},"price_total":86,"transaction_type":"sale","receipt":"neco?","transaction_time":"2015-10-05T15:59:34+0200","created_at":"2015-10-05T15:59:34+0200","updated_at":"2015-10-21T09:20:24+0200"}]}');

        $necktieGateway = new NecktieGateway(
            $this->containerMock,
            $this->entityManagerMock,
            $this->connectorMock,
            $this->productAccessManagerMock,
            $this->tokenStorageMock,
            $this->routerMock,
            $this->necktieGatewayHelperMock
        );

        $invoicesArray = $necktieGateway->getInvoices($this->userMock);

        $this->assertContainsOnlyInstancesOf("AppBundle\\Entity\\Invoice", $invoicesArray);
    }
}