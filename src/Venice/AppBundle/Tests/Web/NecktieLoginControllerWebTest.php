<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 30.01.16
 * Time: 19:59
 */

namespace Venice\AppBundle\Tests\Web;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;


class NecktieLoginControllerWebTest extends WebTestCase
{
    public function testRedirectToNecktieLoginActionReturnsRedirectResponse()
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $this->assertInstanceOf(RedirectResponse::class, $client->getResponse());
    }
}