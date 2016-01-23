<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 19.01.16
 * Time: 16:50
 */

namespace AppBundle\Tests\Services\NecktieGatewayHelper;


use AppBundle\Services\NecktieGatewayHelper;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
    protected $expectedResult;

    /** @var  NecktieGatewayHelper */
    protected $helper;

    public function __construct()
    {
        parent::__construct();

    }

    public function setUp()
    {
        $this->helper = new NecktieGatewayHelper();
    }
}