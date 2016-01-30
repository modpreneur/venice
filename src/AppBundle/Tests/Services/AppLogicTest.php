<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.01.16
 * Time: 21:12
 */

namespace AppBundle\Tests\Services;


use AppBundle\Services\AppLogic;
use AppBundle\Tests\BaseTest;
use Symfony\Component\DependencyInjection\Container;

class AppLogicTest extends BaseTest
{
    protected $containerMock;


    public function setUp()
    {
        $this->containerMock = $this->getMockBuilder(Container::class)
            ->getMock();
    }

    public function testAllMethodsReturnBool()
    {
        $this->containerMock->expects($this->once())
            ->method("hasParameter")
            ->with("necktie_url")
            ->will($this->returnValue(false));

        $appLogic = new AppLogic($this->containerMock);

        $reflection = new \ReflectionClass($appLogic);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if($method->getName() === "__construct") {
                continue;
            }

            $this->assertInternalType("bool", $method->invoke($appLogic));
        }
    }


    public function testAllMethodsReturnsForcedValue()
    {
        $this->containerMock->expects($this->exactly(2))
            ->method("hasParameter")
            ->with("necktie_url")
            ->will($this->returnValue(true));

        $this->assertAllMethodsReturnsForcedValue(true);
        $this->assertAllMethodsReturnsForcedValue(false);
    }


    protected function assertAllMethodsReturnsForcedValue($forcedValue)
    {
        $appLogic = new AppLogic($this->containerMock, $forcedValue);

        $reflection = new \ReflectionClass($appLogic);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if($method->getName() === "__construct" || $method->getName() === "hasForceReturn") {
                continue;
            }

            $this->assertSame($forcedValue, $method->invoke($appLogic));
        }
    }
}