<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account\Member;

use App\Model\Billing\Entity\Account\Member;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testStatus()
    {
        /** @var Member $member */
        /** @var Member $owner */
        ['owner' => $owner, 'member' => $member] = (new TeamBuilder())->getAll();
        $this->assertTrue($owner->isActive());
        $this->assertFalse($member->isActive());
        $member->activate();
        $this->assertTrue($member->isActive());
        $member->block();
        $this->assertFalse($member->isActive());
    }

    public function testTwiceBlock()
    {
        $this->expectExceptionMessage('Already blocked.');
        (new TeamBuilder())->getMember()->block();
    }

    public function testTwiceActivate()
    {
        $this->expectExceptionMessage('Already activated.');
        (new TeamBuilder())->getMember()->activate()->activate();
    }

    public function testOwnerBlock()
    {
        $this->expectExceptionMessage('Cannot block owner.');
        (new TeamBuilder())->getOwner()->block();
    }
}
