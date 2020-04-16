<?php


namespace App\Tests\Unit\Model\Billing\Entity\Account;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    public function testCreate()
    {
        $billingId = '000000';
        $owner = Member::createFromUser((new TeamBuilder())->getUser());
        $team = new Team($owner, $billingId);
        self::assertEquals(0.0, $team->getBallance()->getValue());
        self::assertEquals($owner, $team->getOwner());
        self::assertEquals($team, $owner->getTeam());
        self::assertEquals($billingId, $team->getBillingId());
    }

    public function testCreateWithoutBillingId()
    {
        $this->expectExceptionMessage('Cannot create. Empty $billingId.');
        new Team(Member::createFromUser((new TeamBuilder())->getUser()), '');
    }

    public function testTeamTwiceCreate()
    {
        $this->expectExceptionMessage('Cannot create. $owner has team.');
        new Team((new TeamBuilder())->getOwner(), '--');
    }

}
