<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account\SipAccount;

use App\Model\Billing\Entity\Account\SipAccount;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class SipAccountTest extends TestCase
{

    public function test()
    {
        $member = (new TeamBuilder())->getMember();
        $login = 'login';
        $password = 'password';
        $sipAccount = new SipAccount($member, $login, $password);
        $this->assertSame($member, $sipAccount->getMember());
        $this->assertSame($login, $sipAccount->getLogin());
        $this->assertSame($password, $sipAccount->getPassword());
        $this->assertContains($sipAccount, $member->getSipAccounts());
        $this->assertTrue($sipAccount->isBlocked());
        $this->assertFalse($sipAccount->isActive());
    }

    public function testLogin()
    {
        $sipAccount = (new TeamBuilder())->getSipAccount();

        $login_alpha = 'login_alpha';
        $sipAccount->setLogin($login_alpha);
        $this->assertSame($login_alpha, $sipAccount->getLogin());

        $login_bravo = 'login_bravo';
        $sipAccount->setLogin($login_bravo);
        $this->assertSame($login_bravo, $sipAccount->getLogin());

    }

    public function testSameLogin()
    {
        $member = (new TeamBuilder())->getMember();
        $login = 'login';
        $password = 'password';
        new SipAccount($member, $login, $password);
        $this->expectExceptionMessage('Login already used.');
        new SipAccount($member, $login, $password);
    }

    public function testPassword()
    {

        $sipAccount = (new TeamBuilder())->getSipAccount();
        $password_alpha = 'password_alpha';
        $sipAccount->setPassword($password_alpha);
        $this->assertSame($password_alpha, $sipAccount->getPassword());

        $password_bravo = 'password_bravo';
        $sipAccount->setPassword($password_bravo);
        $this->assertSame($password_bravo, $sipAccount->getPassword());
    }

    public function testEmptyPassword()
    {
        $this->expectExceptionMessage('Can\'t set empty password.');
        (new TeamBuilder())->getSipAccount()->setPassword('');
    }

    public function testAdding()
    {
        $sipAccount = (new TeamBuilder())->getSipAccount();
        $this->expectExceptionMessage('Already added.');
        $sipAccount->getMember()->addSipAccount($sipAccount);
    }

    public function testStatus()
    {
        $sipAccount = (new TeamBuilder())->getSipAccount();
        $sipAccount->activate();
        $this->assertTrue($sipAccount->isActive());
        $this->assertFalse($sipAccount->isBlocked());
        $sipAccount->block();
        $this->assertTrue($sipAccount->isBlocked());
        $this->assertFalse($sipAccount->isActive());
    }

    public function testTwiceActivate()
    {
        $this->expectExceptionMessage('Sip account is already active.');
        (new TeamBuilder())->getSipAccount()->activate()->activate();
    }

    public function testTwiceBlock()
    {
        $this->expectExceptionMessage('Sip account is already blocked.');
        (new TeamBuilder())->getSipAccount()->block();
    }


}
