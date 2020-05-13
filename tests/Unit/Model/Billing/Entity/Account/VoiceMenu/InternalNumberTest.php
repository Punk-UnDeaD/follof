<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class InternalNumberTest extends TestCase
{
    private const NUMBER_ALPHA = '123-1';
    private const NUMBER_BRAVO = '123-2';
    private const NUMBER_LIKE_BRAVO = '1232';

    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_ALPHA));
        $this->assertEquals(self::NUMBER_ALPHA, $voiceMenu->getInternalNumber()->getNumber());
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_BRAVO));
        $this->assertEquals(self::NUMBER_BRAVO, $voiceMenu->getInternalNumber()->getNumber());
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_LIKE_BRAVO));
        $this->assertEquals(self::NUMBER_LIKE_BRAVO, $voiceMenu->getInternalNumber()->getNumber());
    }

    public function testLike()
    {
        $this->expectExceptionMessage('Number can\'t be used.');
        $team = (new TeamBuilder())->getTeam();
        (new VoiceMenu($team))->setInternalNumber(new InternalNumber(self::NUMBER_BRAVO));
        (new VoiceMenu($team))->setInternalNumber(new InternalNumber(self::NUMBER_LIKE_BRAVO));
    }

    public function testLikeMenu()
    {
        $this->expectExceptionMessage('Number can\'t be used.');
        /** @var Member $owner */
        /* @var VoiceMenu $member */
        ['owner' => $owner, 'voiceMenu' => $voiceMenu] = (new TeamBuilder())->getAll();
        $owner->setInternalNumber(new InternalNumber(self::NUMBER_BRAVO));
        $voiceMenu->setInternalNumber(new InternalNumber(self::NUMBER_LIKE_BRAVO));
    }
}
