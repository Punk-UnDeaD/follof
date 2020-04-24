<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseRedirects();
        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertRouteSame('app_login');
    }

    public function testUser()
    {
        $client = static::createClient([], AuthFixture::adminCredentials());
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
}
