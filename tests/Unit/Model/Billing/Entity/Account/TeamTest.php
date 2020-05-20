<?php

namespace App\Tests\Unit\Model\Billing\Entity\Account;

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
        self::assertEquals(0.0, $team->getBalance()->getValue());
        self::assertEquals($user, $team->getUser());
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
}
