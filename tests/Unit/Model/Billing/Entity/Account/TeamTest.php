<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account;

use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\Team;
use App\Model\User\Entity\User\Role;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TeamTest extends TestCase
{
    public function testCreate()
    {
        $billingId = '000000';
        $user = (new TeamBuilder())->getUser();
        $team = new Team($user, $billingId);
        $this->assertSame($user, $team->getUser());
        $this->assertEquals(0.0, $team->getBalance()->getValue());
        $this->assertCount(0, $team->getNumbers());
    }

    public function testCreateWithoutBillingId()
    {
        $this->expectExceptionMessage('Cannot create. Empty $billingId.');
        new Team((new TeamBuilder())->getUser(), '');
    }

    public function testBlockedUser()
    {
        $this->expectExceptionMessage('Cannot create. User not active.');
        new Team((new TeamBuilder())->getUser()->block(), '--');
    }

    public function testRoleUser()
    {
        $this->expectExceptionMessage('Cannot create. Wrong user role.');
        new Team((new TeamBuilder())->getUser()->changeRole(Role::admin()), '--');
    }

    public function testFreeNumber()
    {
        $teamBuilder = new TeamBuilder();
        $team = $teamBuilder->getTeam();
        $this->assertEquals([], $team->getFreeNumbers());
        $number_alpha = (new Number('+7(988)123-45-67'))->setTeam($team);
        $this->assertContains($number_alpha, $team->getFreeNumbers());
        $number_bravo = (new Number('+7(988)123-45-68'))->setTeam($team);
        $this->assertContains($number_bravo, $team->getFreeNumbers());
        $number_charlie = (new Number('+7(988)123-45-69'))->setTeam($team);
        $this->assertContains($number_charlie, $team->getFreeNumbers());
        $teamBuilder->getOwner()->setNumber($number_alpha);
        $this->assertNotContains($number_alpha, $team->getFreeNumbers());
        $teamBuilder->getMember()->setNumber($number_bravo);
        $this->assertNotContains($number_alpha, $team->getFreeNumbers());
        $teamBuilder->getVoiceMenu()->setNumber($number_charlie);
        $this->assertNotContains($number_charlie, $team->getFreeNumbers());
    }
}
