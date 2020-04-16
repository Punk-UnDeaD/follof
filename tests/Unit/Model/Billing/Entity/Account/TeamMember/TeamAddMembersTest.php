<?php


namespace App\Tests\Unit\Model\Billing\Entity\Account\TeamMember;


use App\Model\Billing\Entity\Account\Member;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TeamAddMembersTest extends TestCase
{
    public function testTeamAddMembers()
    {
        ['owner' => $owner, 'team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $this->assertTrue($member->getRole()->isMember());
        $this->assertEquals($team, $member->getTeam());
        $members = $team->getMembers();
        $this->assertCount(2, $members);
        $this->assertContains($owner, $members);
        $this->assertContains($member, $members);
        $member = Member::createTeamMember($team);
        $members = $team->getMembers();
        $this->assertCount(3, $members);
        $this->assertContains($member, $members);
    }

    public function testTeamOwnerAdding()
    {
        ['owner' => $owner, 'team' => $team] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$member can\'t be owner.');
        $team->addMember($owner);
    }

    public function testTeamTwiceAdding()
    {
        ['team' => $team, 'member' => $member] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$member already added.');
        $team->addMember($member);
    }

    public function testTeamAlienAdding()
    {
        ['team' => $team] = (new TeamBuilder())->getAll();
        ['member' => $member] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$member in other Team.');
        $team->addMember($member);
    }

}