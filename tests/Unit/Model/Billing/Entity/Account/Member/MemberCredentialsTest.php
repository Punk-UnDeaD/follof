<?php


namespace App\Tests\Unit\Model\Billing\Entity\Account\Member;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class MemberCredentialsTest extends TestCase
{
    public function testAddCredentials()
    {
        /** @var Member $member */
        /** @var Member $owner */
        ['member' => $member, 'owner' => $owner] = (new TeamBuilder())->getAll();
        $this->assertNull($member->getLogin());
        $this->assertNull($member->getPasswordHash());
        $this->assertNull($owner->getLogin());
        $this->assertNull($owner->getPasswordHash());
        $member->setCredentials($login = 'login', $passwordHash = '~~');
        $this->assertEquals($login, $member->getLogin());
        $this->assertEquals($passwordHash, $member->getPasswordHash());
        $member->setCredentials($login = 'login~', $passwordHash = '~~~');
        $this->assertEquals($login, $member->getLogin());
        $this->assertEquals($passwordHash, $member->getPasswordHash());
    }

    public function testOwner()
    {
        /** @var Member $owner */
        ['owner' => $owner] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('Can\'t set owner credentials.');
        $owner->setCredentials($login = 'login', $passwordHash = '~~');
    }

    public function testEmptyLogin()
    {
        /** @var Member $member */
        ['member' => $member] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('Can\'t set empty login.');
        $member->setCredentials('', '~~');
    }

    public function testEmptyPassword()
    {
        /** @var Member $member */
        ['member' => $member] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('Can\'t set wrong password hash.');
        $member->setCredentials('login', '');
    }

    public function testUsedLogin()
    {
        /** @var Team $team */
        ['team' => $team] = (new TeamBuilder())->getAll();
        $this->expectExceptionMessage('$login already used in Team.');
        Member::createTeamMember($team)->setCredentials('login', '~~');
        Member::createTeamMember($team)->setCredentials('login', '~~');
    }

}
