<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 10.10.15
 * Time: 14:19
 */

namespace AppBundle\Tests\Services;


use AppBundle\Services\ProductAccessManager;
use PHPUnit_Framework_MockObject_MockObject;

class ProductAccessManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityManagerMock;
    protected $userMock;
    protected $productMock;
    protected $productAccessMock;
    protected $repositoryMock;


    public function setUp()
    {
        //mock the user
        $this->userMock = $this
            ->getMockBuilder("AppBundle\\Entity\\User")
            ->disableOriginalConstructor()
            ->getMock();

        //mock the product
        $this->productMock = $this
            ->getMockBuilder("AppBundle\\Entity\\Product\\Product")
            ->disableOriginalConstructor()
            ->getMock();

        //mock the product access
        $this->productAccessMock = $this
            ->getMockBuilder("AppBundle\\Entity\\ProductAccess")
            ->disableOriginalConstructor()
            ->getMock();

        //mock the repository
        $this->repositoryMock = $this
            ->getMockBuilder("\\Doctrine\\ORM\\EntityRepository")
            ->disableOriginalConstructor()
            ->getMock();

        //mock the entity manager
        $this->entityManagerMock = $this
            ->getMockBuilder("\\Doctrine\\Common\\Persistence\\ObjectManager")
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHasAccessToProduct()
    {
        $this->entityManagerMock->expects($this->once())
            ->method("getRepository")
            ->with(
                $this->equalTo("AppBundle:ProductAccess")
            )
            ->will($this->returnValue($this->repositoryMock));

        $this->repositoryMock
            ->expects($this->once())
            ->method("findOneBy")
            ->with(
                $this->isType("array")
            )
            ->will($this->returnValue($this->productAccessMock));
        



        $productAccessManager = new ProductAccessManager($this->entityManagerMock);
        $hasAccess = $productAccessManager->hasAccessToProduct($this->userMock, $this->productMock);

        $this->assertTrue($hasAccess, "Asserting that the user has access to the product.");
    }


    public function testGiveAccessToProduct()
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method("getRepository")
            ->with(
                $this->equalTo("AppBundle:ProductAccess")
            )
            ->will($this->returnValue($this->repositoryMock));

        $this->repositoryMock
            ->expects($this->once())
            ->method("findOneBy")
            ->with(
                $this->isType("array")
            )
            ->will($this->returnValue($this->productAccessMock));

        $this->entityManagerMock
            ->expects($this->never())
            ->method("persist");

        $productAccessManager = new ProductAccessManager($this->entityManagerMock);

        $productAccessManager->giveAccessToProduct($this->userMock, $this->productMock, new \DateTime());
    }
}