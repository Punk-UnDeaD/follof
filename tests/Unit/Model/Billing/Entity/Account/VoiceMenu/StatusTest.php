<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        $voiceMenu->activate();
        $this->assertTrue($voiceMenu->isActive());
        $voiceMenu->block();
        $this->assertFalse($voiceMenu->isActive());
    }

    public function testTwiceBlock()
    {
        $this->expectExceptionMessage('Already blocked.');
        (new TeamBuilder())->getVoiceMenu()->block();
    }

    public function testTwiceActivate()
    {
        $this->expectExceptionMessage('Already activated.');
        (new TeamBuilder())->getVoiceMenu()->activate()->activate();
    }
}
