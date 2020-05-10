<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        $voiceMenu->setInternalNumber(new InternalNumber('444'));
        $voiceMenu->setFile('~~');
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
        (new TeamBuilder())
            ->getVoiceMenu()
            ->setInternalNumber(new InternalNumber('444'))
            ->setFile('~~')
            ->activate()
            ->activate();
    }

    public function testNoFile()
    {
        $this->expectExceptionMessage('File required.');
        (new TeamBuilder())
            ->getVoiceMenu()
            ->setInternalNumber(new InternalNumber('444'))
            ->activate();
    }

    public function testNoNumber()
    {
        $this->expectExceptionMessage('Number required.');
        (new TeamBuilder())
            ->getVoiceMenu()
            ->setFile('~~')
            ->activate();
    }
}
