<?php


namespace App\Tests\Unit\Model\User\Entity\User\SipAccount;


use App\Tests\Builder\User\UserBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;

class AttachTest extends TestCase
{
    public function testAttach()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $login = 'login';
        $password = 'password';
        $user->attachSipAccount($login, $password);
        $sipAccount = $user->getSipAccounts()[0];
        $this->assertEquals($user, $sipAccount->getUser());
        $this->assertEquals($login, $sipAccount->getLogin());
        $this->assertEquals($password, $sipAccount->getPassword());

    }

    public function testExceptionDoubleAttach()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $login = 'login';
        $password = 'password';
        $user->attachSipAccount($login, $password);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Same sip account is already attached.');
        $user->attachSipAccount($login, $password);
    }

}