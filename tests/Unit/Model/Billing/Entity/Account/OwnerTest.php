<?php


namespace App\Tests\Unit\Model\Billing\Entity\Account;


use App\Model\Billing\Entity\Account\Member;
use App\Model\User\Entity\User\Role;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class OwnerTest extends TestCase
{
    public function testCreateFromUser()
    {
        ['user' => $user, 'owner' => $owner] = (new TeamBuilder())->getAll();
        $this->assertEquals($user->getEmail()->getValue(), $owner->getEmail());
        $this->assertTrue($owner->getRole()->isOwner());
    }

    public function testCreateFromNotActiveUser()
    {
        $this->expectExceptionMessage('Cannot create. User not active.');
        Member::createFromUser((new TeamBuilder())->getUser()->block());
    }

    public function testCreateFromWrongRoleUser()
    {
        $this->expectExceptionMessage('Cannot create. Wrong role.');
        Member::createFromUser((new TeamBuilder())->getUser()->changeRole(ROLE::admin()));
    }

}
