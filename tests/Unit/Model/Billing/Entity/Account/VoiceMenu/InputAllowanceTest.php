<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class InputAllowanceTest extends TestCase
{
    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        $voiceMenu->setInputAllowance(true);
        $this->assertTrue($voiceMenu->isInputAllowed());
        $voiceMenu->setInputAllowance(false);
        $this->assertFalse($voiceMenu->isInputAllowed());
    }
}
