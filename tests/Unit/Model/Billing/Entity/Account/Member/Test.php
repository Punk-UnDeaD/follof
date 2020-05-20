<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account\Member;

use App\Model\Billing\Entity\Account\Member;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testOwner()
    {
        $team = (new TeamBuilder())->getTeam();
        $owner = $team->getOwner();
        $this->assertSame($team, $owner->getTeam());
        $this->assertTrue($owner instanceof Member);
        $this->assertTrue($owner->getRole()->isOwner());
        $this->assertContains($owner, $team->getMembers());
        $this->assertEquals('000', $owner->getInternalNumber()->getNumber());
        $this->assertEquals('owner', $owner->getLabel());
        $this->assertNull($owner->getFallbackNumber());
    }

    public function testMember()
    {
        $team = (new TeamBuilder())->getTeam();
        $member = new Member($team);
        $this->assertSame($team, $member->getTeam());
        $this->assertTrue($member->getRole()->isMember());
        $this->assertContains($member, $team->getMembers());
        $this->assertNull($member->getInternalNumber());
        $this->assertRegExp('/member-\d+/', $member->getLabel());
        $this->assertNull($member->getFallbackNumber());
    }
}
