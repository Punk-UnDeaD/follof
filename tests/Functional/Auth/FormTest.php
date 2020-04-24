<?php

namespace App\Tests\Functional\Auth;

use App\Tests\Functional\AuthFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $form = [
            'email' => AuthFixture::adminCredentials()['PHP_AUTH_USER'],
            'password' => AuthFixture::adminCredentials()['PHP_AUTH_PW'],
        ];
        $client->submitForm('Login', $form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('home');
    }

    public function testWrongPassword()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $form = [
            'email' => AuthFixture::adminCredentials()['PHP_AUTH_USER'],
            'password' => AuthFixture::adminCredentials()['PHP_AUTH_PW'].'!',
        ];
        $client->submitForm('Login', $form);
        $this->assertResponseRedirects();
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_login');
        $this->assertSelectorTextContains('.alert-danger', 'Invalid credentials.');
    }

    public function testWrongEmail()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $form = [
            'email' => AuthFixture::adminCredentials()['PHP_AUTH_USER'].'!',
            'password' => AuthFixture::adminCredentials()['PHP_AUTH_PW'],
        ];
        $client->submitForm('Login', $form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_login');
        $this->assertSelectorTextContains('.alert-danger', 'Username could not be found.');
    }

    public function testWrongCSRF()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $form = [
            'email' => AuthFixture::adminCredentials()['PHP_AUTH_USER'].'!',
            'password' => AuthFixture::adminCredentials()['PHP_AUTH_PW'],
            '_csrf_token' => '!',
        ];
        $client->submitForm('Login', $form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_login');
        $this->assertSelectorTextContains('.alert-danger', 'Invalid CSRF token.');
    }
}
