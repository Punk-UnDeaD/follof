<?php


namespace App\Tests\Unit\Model\User\Entity\User\SipAccount;


use App\Model\User\Entity\User\SipAccount;
use App\Tests\Builder\User\SipAccountBuilder;
use App\Tests\Builder\User\UserBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BlockTest extends TestCase
{
    public function testActivate()
    {
        $sipAccount = SipAccountBuilder::create();
        $sipAccount->activate();
        $sipAccount->block();
        $this->assertTrue($sipAccount->isBlocked());
        $this->assertFalse($sipAccount->isActive());
    }

    public function testExceptionDoubleBlock()
    {
        $sipAccount = SipAccountBuilder::create();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Sip account is already blocked.');
        $sipAccount->block();
    }


}