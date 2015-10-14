<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 10.10.15
 * Time: 12:22
 */

namespace AppBundle\Tests\Services;


use AppBundle\Services\Connector;
use PHPUnit_Framework_MockObject_MockObject;

class ConnectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $curlMock;

    public function setUp()
    {
        $this->curlMock = $this
            ->getMockBuilder("Anchovy\\CURLBundle\\CURL\\Curl")
            ->setConstructorArgs([[]])
            ->getMock();
    }

    public function testGetJson()
    {
        $this->curlMock->expects($this->once())
             ->method("setMethod")
             ->with(
                 $this->stringContains("GET")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("setURL")
             ->with(
                 $this->stringContains("http://")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue('{"greeting": "hello"}'));

        $connector = new Connector($this->curlMock);

        $response = $connector->getJson("http://");

        $this->assertEquals(["greeting" => "hello"], $response);
    }

    public function testPutAndGetJson()
    {
        $this->curlMock->expects($this->once())
             ->method("setMethod")
             ->with(
                 $this->stringContains("PUT"),
                 $this->isType("array")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("setURL")
             ->with(
                 $this->stringContains("http://")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue('{"greeting": "hello"}'));

        $connector = new Connector($this->curlMock);

        $response = $connector->putAndGetJson("http://", []);

        $this->assertEquals(["greeting" => "hello"], $response);
    }

    public function testPostAndGetJson()
    {
        $this->curlMock->expects($this->once())
             ->method("setMethod")
             ->with(
                 $this->stringContains("POST"),
                 $this->isType("array")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("setURL")
             ->with(
                 $this->stringContains("http://")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue('{"greeting": "hello"}'));

        $connector = new Connector($this->curlMock);

        $response = $connector->postAndGetJson("http://");

        $this->assertEquals(["greeting" => "hello"], $response);
    }

    public function testPostJson()
    {
        $this->curlMock->expects($this->once())
             ->method("setMethod")
             ->with(
                 $this->stringContains("POST"),
                 $this->isType("array")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("setURL")
             ->with(
                 $this->stringContains("http://")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->atLeast(3))
             ->method("setOption")
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue('{"greeting": "hello"}'));

        $connector = new Connector($this->curlMock);

        $response = $connector->postJson("http://", "data");

        $this->assertEquals(["greeting" => "hello"], $response);
    }

    public function testGet()
    {
        $this->curlMock->expects($this->once())
             ->method("setMethod")
             ->with(
                 $this->stringContains("GET"),
                 $this->isType("array")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("setURL")
             ->with(
                 $this->stringContains("http://")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->atLeast(1))
             ->method("setOption")
             ->will($this->returnSelf());

        $this->curlMock->expects($this->atLeast(1))
             ->method("setOptions")
             ->with(
                 $this->isType("array")
             )
             ->will($this->returnSelf());

        $this->curlMock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue('response'));

        $connector = new Connector($this->curlMock);

        $response = $connector->get("http://", "data");

        $this->assertEquals("response", $response);
    }
}