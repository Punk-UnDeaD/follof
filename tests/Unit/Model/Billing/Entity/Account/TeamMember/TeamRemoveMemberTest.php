<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account\TeamMember;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TeamRemoveMemberTest extends TestCase
{
    public function testTeamRemoveMember()
    {
        ['team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $team->removeMember($member);
        self::assertNotContains($member, $team->getMembers());
    }

    public function testTeamRemoveMemberTwice()
    {
        ['team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $team->removeMember($member);
        $this->expectExceptionMessage('$member already not in Team.');
        $team->removeMember($member);
    }

    public function testTeamRemoveOwner()
    {
        ['owner' => $owner, 'team' => $team] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('Can\'t remove owner.');
        $team->removeMember($owner);
    }

    public function testTeamRemoveAlien()
    {
        ['member' => $member] = (new TeamBuilder())->getAll();
        ['team' => $team] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$member in other Team.');
        $team->removeMember($member);
    }

}
