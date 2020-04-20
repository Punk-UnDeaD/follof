<?php


namespace App\Tests\Unit\Model\Billing\Entity\Account\TeamMember;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TeamAddMembersTest extends TestCase
{
    public function testTeamAddMembers()
    {
        /** @var Member $member */
        /** @var Member $owner */
        /** @var Team $team */
        ['owner' => $owner, 'team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $this->assertTrue($member->getRole()->isMember());
        $this->assertEquals($team, $member->getTeam());

        $this->assertCount(2, $team->getMembers());
        $this->assertContains($owner, $team->getMembers());
        $this->assertContains($member, $team->getMembers());
        $member = new Member($team);
        $this->assertCount(3, $team->getMembers());
        $this->assertContains($member, $team->getMembers());
    }

    public function testTeamTwiceAdding()
    {
        /** @var Member $member */
        /** @var Team $team */
        ['team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$member already added.');
        $team->addMember($member);
    }

    public function testTeamAlienAdding()
    {
        $this->expectExceptionMessage('$member in other Team.');
        (new TeamBuilder())->getTeam()->addMember((new TeamBuilder())->getMember());
    }

}
