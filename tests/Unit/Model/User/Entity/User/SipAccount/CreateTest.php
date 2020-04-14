<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SipAccount;

use App\Model\User\Entity\User\SipAccount;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class CreateTest extends TestCase
{
    public function testCreate()
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();
        $login = 'login';
        $password = 'password';
        $sipAccount = new SipAccount($user, $login, $password);
        $this->assertEquals($sipAccount->getUser(), $user);
        $this->assertEquals($sipAccount->getLogin(), $login);
        $this->assertEquals($sipAccount->getPassword(), $password);
        $this->assertTrue($sipAccount->isBlocked());
        $this->assertFalse($sipAccount->isActive());
    }
}