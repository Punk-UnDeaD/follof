<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\Number;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class TestWithEndpoints extends TestCase
{
    private const NUMBER = '+7(988)123-45-78';

    public function test()
    {
        $number = new Number(self::NUMBER);
        $team = (new TeamBuilder())->getTeam();
        $member = new Member($team);
        $this->assertNull($member->getNumber());
        $number->setTeam($team);
        $member->setNumber($number);
        $this->assertSame($number, $member->getNumber());
        $menu = new VoiceMenu($team);
        $member->setNumber();
        $this->assertNull($member->getNumber());
        $menu->setNumber($number);
        $this->assertSame($number, $menu->getNumber());
        $team_bravo = (new TeamBuilder())->getTeam();
        $number->setTeam($team_bravo);
        $this->assertNull($menu->getNumber());
    }

    public function testAlienTeamNumber()
    {
        $number = new Number(self::NUMBER);
        $member = (new TeamBuilder())->getMember();
        $team = (new TeamBuilder())->getTeam();
        $number->setTeam($team);
        $this->expectExceptionMessage('Team can\'t contain this Number.');
        $member->setNumber($number);
    }

    public function testAlienPointNumber()
    {
        $teamBuilder = (new TeamBuilder());
        $number = new Number(self::NUMBER);
        $number->setTeam($teamBuilder->getTeam());
        $teamBuilder->getMember()->setNumber($number);
        $this->expectExceptionMessage('Number not free.');
        $teamBuilder->getVoiceMenu()->setNumber($number);
    }
}
