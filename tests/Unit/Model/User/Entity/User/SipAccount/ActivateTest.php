<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\User\Entity\User\SipAccount;

use App\Tests\Builder\User\SipAccountBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;

class ActivateTest extends TestCase
{
    public function testActivate()
    {
        $sipAccount = SipAccountBuilder::create();
        $sipAccount->activate();
        $this->assertTrue($sipAccount->isActive());
        $this->assertFalse($sipAccount->isBlocked());
    }

    public function testExceptionDoubleActivate()
    {
        $sipAccount = SipAccountBuilder::create();
        $sipAccount->activate();
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Sip account is already active.');
        $sipAccount->activate();
    }

}