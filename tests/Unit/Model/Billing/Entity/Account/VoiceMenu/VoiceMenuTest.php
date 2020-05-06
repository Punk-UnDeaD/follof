<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class VoiceMenuTest extends TestCase
{
    private const NUMBER_ALPHA = '123';
    private const NUMBER_BRAVO = '123-45';
    private const FILE_ALPHA = '~/voice_alpha.mp3';
    private const FILE_BRAVO = '~/voice_bravo.mp3';

    public function test()
    {
        $team = (new TeamBuilder())->getTeam();
        $voiceMenu = new VoiceMenu($team, new InternalNumber(self::NUMBER_ALPHA), self::FILE_ALPHA);
        $this->assertEquals($team, $voiceMenu->getTeam());
        $this->assertEquals(self::NUMBER_ALPHA, $voiceMenu->getInternalNumber()->getNumber());
        $this->assertEquals(self::FILE_ALPHA, $voiceMenu->getFile());
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_ALPHA));
        $this->assertEquals(self::NUMBER_ALPHA, $voiceMenu->getInternalNumber()->getNumber());
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_BRAVO));
        $this->assertEquals(self::NUMBER_BRAVO, $voiceMenu->getInternalNumber()->getNumber());
        $voiceMenu->setFile(self::FILE_BRAVO);
        $this->assertEquals(self::FILE_BRAVO, $voiceMenu->getFile());
        $this->assertEquals([], $voiceMenu->getPoints());
        $this->assertFalse($voiceMenu->isActive());

    }

    public function testAdding()
    {
        $team = (new TeamBuilder())->getTeam();
        $voiceMenu = new VoiceMenu($team, new InternalNumber(self::NUMBER_ALPHA), self::FILE_ALPHA);
        $this->expectExceptionMessage('Already added.');
        $team->addVoiceMenu($voiceMenu);
    }

    public function testNumberCreate()
    {
        $team = (new TeamBuilder())->getTeam();
        $this->expectExceptionMessage('Number can\'t be used.');
        new VoiceMenu($team, new InternalNumber(self::NUMBER_ALPHA), self::FILE_ALPHA);
        new VoiceMenu($team, new InternalNumber(self::NUMBER_ALPHA), self::FILE_ALPHA);
    }

    public function testNumberSet()
    {
        $team = (new TeamBuilder())->getTeam();
        $this->expectExceptionMessage('Number can\'t be used.');
        new VoiceMenu($team, new InternalNumber(self::NUMBER_ALPHA), self::FILE_ALPHA);
        $voiceMenu = new VoiceMenu($team, new InternalNumber(self::NUMBER_BRAVO), self::FILE_ALPHA);
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_ALPHA));
    }

}
