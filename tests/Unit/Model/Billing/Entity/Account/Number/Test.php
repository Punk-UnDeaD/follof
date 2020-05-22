<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\Number;

use App\Model\Billing\Entity\Account\Number;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    private const NUMBER = '+7(988)123-45-78';
    private const NUMBER_WRONG = '+7(988)123-45-7';

    public function test()
    {
        $number = new Number(self::NUMBER);
        $this->assertEquals(self::NUMBER, $number->getNumber());
        $this->assertNull($number->getTeam());
        $team = (new TeamBuilder())->getTeam();
        $number->setTeam($team);
        $this->assertSame($team, $number->getTeam());
        $this->assertContains($number, $team->getNumbers());
        $number->setTeam();
        $this->assertNull($number->getTeam());
        $this->assertNotContains($number, $team->getNumbers());
        $number->setTeam($team);
        $new_team = (new TeamBuilder())->getTeam();
        $number->setTeam($new_team);
        $this->assertNotContains($number, $team->getNumbers());
        $this->assertContains($number, $new_team->getNumbers());
        $this->assertSame($new_team, $number->getTeam());
    }

    public function testWrongFormat()
    {
        $this->expectExceptionMessage('Wrong format.');
        new Number(self::NUMBER_WRONG);
    }

    public function testAdding()
    {
        $this->expectExceptionMessage('Number not for team.');
        $team = (new TeamBuilder())->getTeam();
        $number = new Number(self::NUMBER);

        $team->addNumber($number);
    }

    public function testTwiceAdding()
    {
        $this->expectExceptionMessage('Already contains.');
        $team = (new TeamBuilder())->getTeam();
        $number = new Number(self::NUMBER);
        $number->setTeam($team);
        $team->addNumber($number);
    }

    public function testAlienAdding()
    {
        $this->expectExceptionMessage('Number not for team.');
        $team = (new TeamBuilder())->getTeam();
        $number = new Number(self::NUMBER);
        $number->setTeam($team);
        (new TeamBuilder())->getTeam()->addNumber($number);
    }

    public function testRemove()
    {
        $this->expectExceptionMessage('Can\'t remove.');
        $team = (new TeamBuilder())->getTeam();
        $number = new Number(self::NUMBER);
        $number->setTeam($team);
        $team->removeNumber($number);
    }

    public function testRemoveTwice()
    {
        $this->expectExceptionMessage('Already not contains.');
        $team = (new TeamBuilder())->getTeam();
        $number = new Number(self::NUMBER);
        $number->setTeam($team)->setTeam();
        $team->removeNumber($number);
    }

}
