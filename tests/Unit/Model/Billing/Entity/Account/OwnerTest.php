<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class OwnerTest extends TestCase
{
    public function testOwner()
    {
        /** @var Team $team */
        ['team' => $team] = (new TeamBuilder())->getAll();
        $owner = $team->getOwner();
        $this->assertEquals($team, $owner->getTeam(), );
        $this->assertTrue($owner instanceof Member);
        $this->assertTrue($owner->getRole()->isOwner());
        $this->assertContains($owner, $team->getMembers());
    }
}
