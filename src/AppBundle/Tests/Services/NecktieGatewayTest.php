<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 26.01.16
 * Time: 11:56
 */

namespace AppBundle\Tests\Services;


use AppBundle\Entity\OAuthToken;
use AppBundle\Entity\Product\ProductRepository;
use AppBundle\Entity\Product\StandardProduct;
use AppBundle\Entity\Repositories\UserRepository;
use AppBundle\Entity\User;
use AppBundle\Services\NecktieConnector;
use AppBundle\Services\NecktieGateway;
use AppBundle\Services\NecktieGatewayHelper;
use AppBundle\Tests\BaseTest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\RouterInterface;

class NecktieGatewayTest extends BaseTest
{
    protected $necktieUrl = "http://necktie.com";
    protected $necktieClientId = "client_id_value";
    protected $necktieClientSecret = "client_secret_value";
    protected $loginResponseRoute = "loginResponseRoute";

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $entityManagerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $connectorMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $routerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $helperMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $userMock;

    /** @var  NecktieGateway */
    protected $necktieGateway;

    /** @var  \PHPUnit_Framework_MockObject_MockBuilder */
    protected $necktieGatewayMockBuilder;

    public function setUp()
    {
        $this->entityManagerMock = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->connectorMock = $this
            ->getMockBuilder(NecktieConnector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this
            ->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helperMock = $this
            ->getMockBuilder(NecktieGatewayHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMock = $this
            ->getMockBuilder(User::class)
            ->getMock();

        $this->necktieGateway = new NecktieGateway(
            $this->entityManagerMock,
            $this->routerMock,
            $this->helperMock,
            $this->connectorMock,
            $this->necktieUrl,
            $this->necktieClientId,
            $this->necktieClientSecret,
            $this->loginResponseRoute
        );

        $this->necktieGatewayMockBuilder =
            $this->getMockBuilder(NecktieGateway::class)
                ->setConstructorArgs(
                    [
                        $this->entityManagerMock,
                        $this->routerMock,
                        $this->helperMock,
                        $this->connectorMock,
                        $this->necktieUrl,
                        $this->necktieClientId,
                        $this->necktieClientSecret,
                        $this->loginResponseRoute
                    ]
                );
    }


    public function testGetLoginUrl()
    {
        $necktieGatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["createStateCookie"])
            ->getMock();

        $necktieGatewayMock->expects($this->once())
            ->method("createStateCookie")
            ->willReturn(new Cookie("state", "stateCookieString"));

        $url = $necktieGatewayMock->getLoginUrl();

        $this->assertEquals(
            "http://necktie.com/oauth/v2/auth?client_id=client_id_value&client_secret=client_secret_value&grant_type=trusted_authorization&state=stateCookieString",
            $url
        );

        $cookie = $necktieGatewayMock->getStateCookie();
        $this->assertInstanceOf(Cookie::class, $cookie);
    }


    public function testCreateStateCookie()
    {
        $createdCookie = $this->invokeMethod($this->necktieGateway, "createStateCookie");

        $this->assertInstanceOf(Cookie::class, $createdCookie);
    }


    public function testGetHelper()
    {
        $this->assertEquals($this->helperMock, $this->necktieGateway->getHelper());
    }


    public function testCreateNewUserNoPersist()
    {
        $expectedUser = new User();
        $expectedUser->setNecktieId(3)
            ->setEmail("email@mail.com")
            ->setUsername("username");

        $user = $this->invokeMethod($this->necktieGateway, "createNewUser", [["username" => "username", "email" => "email@mail.com", "id" => 3], false]);

        $this->assertEquals($expectedUser->getNecktieId(), $user->getNecktieId());
        $this->assertEquals($expectedUser->getUsername(), $user->getUsername());
        $this->assertEquals($expectedUser->getEmail(), $user->getEmail());
    }


    public function testCreateNewUserPersist()
    {
        $expectedUser = new User();
        $expectedUser->setNecktieId(3)
            ->setEmail("email@mail.com")
            ->setUsername("username");

        $this->entityManagerMock
            ->expects($this->once())
            ->method("persist")
            ->with($expectedUser);

        $this->entityManagerMock
            ->expects($this->once())
            ->method("flush");


        $user = $this->invokeMethod($this->necktieGateway, "createNewUser", [["username" => "username", "email" => "email@mail.com", "id" => 3], true]);

        $this->assertEquals($expectedUser, $user);
    }


    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Could not get token from response.
     */
    public function testRefreshAccessTokenNoResponse()
    {
        $this->helperMock
            ->expects($this->never())
            ->method("isRefreshTokenExpiredResponse");

        $this->refreshAccessToken(null, false);
    }


    /**
     * @expectedException AppBundle\Exceptions\ExpiredRefreshTokenException
     */
    public function testRefreshAccessTokenExpiredTokenResponse()
    {
        $connectorResponse = "refreshTokenExpiredResponse";

        $this->helperMock
            ->expects($this->once())
            ->method("isRefreshTokenExpiredResponse")
            ->with($connectorResponse)
            ->will($this->returnValue(true));

        $this->refreshAccessToken($connectorResponse, true);
    }


    public function testRefreshAccessTokenTokenCreated()
    {
        $connectorResponse = ["refresh_token" => "ghbjknljiugyfc"];

        $this->helperMock
            ->expects($this->once())
            ->method("isRefreshTokenExpiredResponse")
            ->with($connectorResponse)
            ->will($this->returnValue(false));

        $OAuthTokenMock = $this->getMockBuilder(OAuthToken::class)
            ->getMock();

        $OAuthTokenMock
            ->expects($this->once())
            ->method("setUser")
            ->with($this->userMock);

        $this->helperMock
            ->expects($this->once())
            ->method("createOAuthTokenFromArray")
            ->with($connectorResponse)
            ->will($this->returnValue($OAuthTokenMock));

        $this->userMock
            ->expects($this->once())
            ->method("addOAuthToken")
            ->with($OAuthTokenMock);

        $this->refreshAccessToken($connectorResponse);
    }


    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Could not get token from response.
     */
    public function testRefreshAccessTokenTokenNotCreated()
    {
        $connectorResponse = ["refresh_token" => "ghbjknljiugyfc"];

        $this->helperMock
            ->expects($this->once())
            ->method("isRefreshTokenExpiredResponse")
            ->with($connectorResponse)
            ->will($this->returnValue(false));

        $OAuthTokenMock = $this->getMockBuilder(OAuthToken::class)
            ->getMock();

        $this->helperMock
            ->expects($this->once())
            ->method("createOAuthTokenFromArray")
            ->with($connectorResponse)
            ->will($this->returnValue(null));

        $OAuthTokenMock
            ->expects($this->never())
            ->method("setUser")
            ->with($this->userMock);

        $this->userMock
            ->expects($this->never())
            ->method("addOAuthToken");

        $this->refreshAccessToken($connectorResponse);
    }


    public function testRefreshAccessTokenIfNeededValidLastToken()
    {
        $necktieGatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["createStateCookie"])
            ->getMock();

        // Add 10 minutes to the current date
        $validTo = new \DateTime();
        $validTo->setTimestamp($validTo->getTimestamp() + 10 * 60);
        $token = (new OAuthToken())->setValidTo($validTo);

        $this->userMock
            ->expects($this->once())
            ->method("isLastAccessTokenValid")
            ->will($this->returnValue(true));

        $this->userMock
            ->expects($this->once())
            ->method("getLastToken")
            ->will($this->returnValue($token));

        $necktieGatewayMock
            ->expects($this->never())
            ->method("refreshAccessToken");

        $necktieGatewayMock->refreshAccessTokenIfNeeded($this->userMock);
    }


    public function testRefreshAccessTokenIfNeededInvalidLastToken()
    {
        $necktieGatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["createStateCookie", "refreshAccessToken"])
            ->getMock();

        // Add 10 minutes to the current date
        $validTo = new \DateTime();
        $validTo->setTimestamp($validTo->getTimestamp() + 10 * 60);
        $token = (new OAuthToken())->setValidTo($validTo);

        $this->userMock
            ->expects($this->once())
            ->method("isLastAccessTokenValid")
            ->will($this->returnValue(false));

        $this->userMock
            ->expects($this->once())
            ->method("getLastToken")
            ->will($this->returnValue($token));


        $necktieGatewayMock
            ->expects($this->once())
            ->method("refreshAccessToken")
            ->will($this->returnValue(null));

        $necktieGatewayMock->refreshAccessTokenIfNeeded($this->userMock);

    }


    public function testGetUserByAccessTokenExistingUser()
    {
        $responseData = ["id" => 3, "username" => "username", "email" => "email@mail.com"];
        $jsonResponseData = json_encode($responseData);

        $userInfo = [
            "id" => 3,
            "username" => "username",
            "email" => "email@mail.com"
        ];

        $repositoryUser = new User();
        $repositoryUser->setNecktieId(3)
            ->setUsername("username")
            ->setEmail("email@mail.com");

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["username" => $responseData["username"]])
            ->will($this->returnValue($repositoryUser));

        $this->entityManagerMock
            ->expects($this->once())
            ->method("getRepository")
            ->with("AppBundle:User")
            ->will($this->returnValue($repositoryMock));

        $foundUser = $this->getUserByAccessToken(
            $responseData,
            $userInfo,
            false,
            false
        );

        $this->assertEquals($repositoryUser->getNecktieId(), $foundUser->getNecktieId());
        $this->assertEquals($repositoryUser->getUsername(), $foundUser->getUsername());
        $this->assertEquals($repositoryUser->getEmail(), $foundUser->getEmail());
    }


    public function testGetUserByAccessTokenNoUserInfo()
    {
        $responseData = [];

        $userInfo = [
            "id" => 3,
            "username" => "username",
            "email" => "email@mail.com"
        ];

        $repositoryUser = new User();
        $repositoryUser->setNecktieId(3)
            ->setUsername("username")
            ->setEmail("email@mail.com");

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->never())
            ->method("findOneBy");

        $this->entityManagerMock
            ->expects($this->never())
            ->method("getRepository");

        $foundUser = $this->getUserByAccessToken(
            $responseData,
            [],
            false,
            false
        );

        $this->assertNull($foundUser);
    }


    public function testGetUserByAccessTokenNoUserFoundDoNotCreate()
    {
        $responseData = ["id" => 3, "username" => "username", "email" => "email@mail.com"];

        $userInfo = [
            "id" => 3,
            "username" => "username",
            "email" => "email@mail.com"
        ];

        $repositoryUser = new User();
        $repositoryUser->setNecktieId(3)
            ->setUsername("username")
            ->setEmail("email@mail.com");

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["username" => $responseData["username"]])
            ->will($this->returnValue(null));

        $this->entityManagerMock
            ->expects($this->once())
            ->method("getRepository")
            ->with("AppBundle:User")
            ->will($this->returnValue($repositoryMock));

        $foundUser = $this->getUserByAccessToken(
            $responseData,
            $userInfo,
            false,
            false,
            ["createNewUser"]
        );

        $this->assertNull($foundUser);
    }


    public function testGetUserByAccessTokenNoUserFoundDoCreate()
    {
        $responseData = ["id" => 3, "username" => "username", "email" => "email@mail.com"];
        $jsonResponseData = json_encode($responseData);

        $userInfo = [
            "id" => 3,
            "username" => "username",
            "email" => "email@mail.com"
        ];

        $repositoryUser = new User();
        $repositoryUser->setNecktieId(3)
            ->setUsername("username")
            ->setEmail("email@mail.com");

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method("findOneBy")
            ->with(["username" => $responseData["username"]])
            ->will($this->returnValue(null));

        $this->entityManagerMock
            ->expects($this->once())
            ->method("getRepository")
            ->with("AppBundle:User")
            ->will($this->returnValue($repositoryMock));

        $result = $this->getUserByAccessToken(
            $responseData,
            $userInfo,
            true,
            false
        );

        $this->assertEquals("createNewUserResult", $result);
    }


    public function testGetUserByAccessTokenInvalidResponse()
    {
        $userInfo = [
            "id" => 3,
            "username" => "username",
            "email" => "email@mail.com"
        ];

        $repositoryUser = new User();
        $repositoryUser->setNecktieId(3)
            ->setUsername("username")
            ->setEmail("email@mail.com");

        $repositoryMock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->never())
            ->method("findOneBy");

        $this->entityManagerMock
            ->expects($this->never())
            ->method("getRepository");

        $result = $this->getUserByAccessToken(
            "invalid response data",
            $userInfo,
            true,
            false
        );

        $this->assertEquals(null, $result);
    }


    protected function getUserByAccessToken($responseData, $userInfo, $createNewUser, $persistNewUser)
    {
        $accessToken = "accessTokenString";

        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with(null, "GET", "api/v1/profile", [], $accessToken)
            ->will($this->returnValue(json_encode($responseData)));

        $this->helperMock
            ->expects($this->any())
            ->method("getUserInfoFromNecktieProfileResponse")
            ->with($responseData)
            ->will($this->returnValue($userInfo));

        $necktieGatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["createNewUser"])
            ->getMock();

        $necktieGatewayMock
            ->expects($this->any())
            ->method("createNewUser")
            ->with($userInfo, $persistNewUser)
            ->will($this->returnValue("createNewUserResult"));

        return $necktieGatewayMock->getUserByAccessToken($accessToken, $createNewUser, $persistNewUser);

    }


    public function testGetInvoicesSuccessfully()
    {
        $responseData = [
            "invoices" => [

            ]
        ];

        $jsonResponseData = json_encode($responseData);

        $gatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["refreshAccessTokenIfNeeded"])
            ->getMock();

        $gatewayMock
            ->expects($this->once())
            ->method("refreshAccessTokenIfNeeded")
            ->with($this->userMock);

        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with($this->userMock, "GET", "api/v1/invoices", ["withItems" => true])
            ->will($this->returnValue($jsonResponseData));

        $this->helperMock
            ->expects($this->once())
            ->method("getInvoicesFromNecktieResponse")
            ->with($responseData)
            ->will($this->returnValue(["invoice1", "invoice2"]));

        $invoices = $gatewayMock->getInvoices($this->userMock);

        $this->assertNotEmpty($invoices);
    }


    public function testGetInvoicesResponseNotArray()
    {
        $responseData = null;

        $gatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["refreshAccessTokenIfNeeded"])
            ->getMock();

        $gatewayMock
            ->expects($this->once())
            ->method("refreshAccessTokenIfNeeded")
            ->with($this->userMock);

        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with($this->userMock, "GET", "api/v1/invoices", ["withItems" => true])
            ->will($this->returnValue($responseData));

        $this->helperMock
            ->expects($this->never())
            ->method("getInvoicesFromNecktieResponse");

        $invoices = $gatewayMock->getInvoices($this->userMock);

        $this->assertEmpty($invoices);
    }


    public function testGetInvoicesResponseNoInvoicesKey()
    {
        $responseData = [];
        $jsonResponseData = json_encode($responseData);

        $gatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["refreshAccessTokenIfNeeded"])
            ->getMock();

        $gatewayMock
            ->expects($this->once())
            ->method("refreshAccessTokenIfNeeded")
            ->with($this->userMock);

        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with($this->userMock, "GET", "api/v1/invoices", ["withItems" => true])
            ->will($this->returnValue($jsonResponseData));

        $this->helperMock
            ->expects($this->never())
            ->method("getInvoicesFromNecktieResponse");

        $invoices = $gatewayMock->getInvoices($this->userMock);

        $this->assertEmpty($invoices);
    }

    /**
     * @dataProvider providerTestUpdateProductAccessesUnsuccessfulResponse
     */
    public function testUpdateProductAccesses($responseData, $expectedResult)
    {
        $gatewayMock = $this->necktieGatewayMockBuilder
            ->setMethods(["refreshAccessTokenIfNeeded"])
            ->getMock();

        $gatewayMock
            ->expects($this->once())
            ->method("refreshAccessTokenIfNeeded")
            ->with($this->userMock);

        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with($this->userMock, "GET", "api/v1/product-accesses")
            ->will($this->returnValue(json_encode($responseData)));

        $repositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManagerMock
            ->expects($this->any())
            ->method("getRepository")
            ->with(StandardProduct::class)
            ->will($this->returnValue($repositoryMock));

        $repositoryMock
            ->expects($this->any())
            ->method("findOneBy")
            ->will($this->returnCallback([$this, 'getProduct']));

        $this->userMock
            ->expects($this->any())
            ->method("giveAccessToProduct")
            ->will($this->returnValue("givenProductAccess"));

        $accesses = $gatewayMock->updateProductAccesses($this->userMock);

        $this->assertEquals($expectedResult, $accesses);
    }

    public function providerTestUpdateProductAccessesUnsuccessfulResponse()
    {
        return [
            //#0 data set - response == null
            [
                //response data
                null,
                //expected data
                [],
                // changes to user mock
            ],
            //#1 data set - no productAccess key
            [
                //response data
                [],
                //expected data
                [],
                // changes to user mock
            ],
            //#2 data set - productAccess does not contain array
            [
                //response data
                ["productAccesses" => null],
                //expected data
                [],
                // changes to user mock
            ],
            //#3 data set - productAccess array does not have keys product and from_date
            [
                //response data
                ["productAccesses" => []],
                //expected data
                [],
                // changes to user mock
            ],
            //#4 data set - productAccess array does not have keys product and from_date
            [
                //response data
                ["productAccesses" => []],
                //expected data
                [],
                // changes to user mock
            ],
            //#5 data set - product not found
            [
                //response data
                ["productAccesses" => [
                    [
                        "product" => 0,
                        "from_date" => "2016-03-04T00:00:00+0100"
                    ]
                ]],
                //expected data
                [],
                // changes to user mock
            ],
            //#6 data set - from date is not valid
            [
                //response data
                ["productAccesses" => [
                    [
                        "product" => 0,
                        "from_date" => "invalid"
                    ]
                ]],
                //expected data
                [],
                // changes to user mock
            ],
            //#7 data set - product found
            [
                //response data
                ["productAccesses" => [
                    [
                        "product" => 1,
                        "from_date" => "2016-03-04T00:00:00+0100",
                        "id" => 3 //necktie id
                    ]
                ]],
                //expected data
                [
                    "givenProductAccess"
                ],
                // changes to user mock
            ],
            //#8 data set - product found; present to_date field
            [
                //response data
                ["productAccesses" => [
                    [
                        "product" => 1,
                        "from_date" => "2016-03-04T00:00:00+0100",
                        "to_date" => "2016-03-05T00:00:00+0100",
                        "id" => 3 //necktie id
                    ]
                ]],
                //expected data
                ["givenProductAccess"],
                // changes to user mock

            ],
            //#9 data set - product found; invalid from_date
            [
                //response data
                ["productAccesses" => [
                    [
                        "product" => 1,
                        "from_date" => "invalid",
                        "to_date" => "2016-03-05T00:00:00+0100",
                        "id" => 3 //necktie id
                    ]
                ]],
                //expected data
                [],
                // changes to user mock

            ],
        ];
    }


    protected function refreshAccessToken($connectorResponse)
    {
        $this->userMock
            ->expects($this->once())
            ->method("getLastRefreshToken")
            ->will($this->returnValue("lastRefreshToken"));


        $this->connectorMock
            ->expects($this->once())
            ->method("getResponse")
            ->with(
                $this->userMock,
                "POST",
                "oauth/v2/token",
                [
                    "client_id" => $this->necktieClientId,
                    "client_secret" => $this->necktieClientSecret,
                    "grant_type" => "refresh_token",
                    "refresh_token" => "lastRefreshToken"

                ]
            )
            ->will($this->returnValue(json_encode($connectorResponse)));

        $this->necktieGateway->refreshAccessToken($this->userMock);
    }

    public function getProduct($array)
    {
        if ($array["necktieId"] == 1) {
            return new StandardProduct();

        } else {
            return null;
        }

    }

}