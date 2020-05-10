<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Billing\Entity\Account\VoiceMenu;

use App\Tests\Builder\Billing\TeamBuilder;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    const LABEL_ALPHA = 'label alpha';
    const LABEL_BRAVO = 'label bravo';

    public function test()
    {
        $voiceMenu = (new TeamBuilder())->getVoiceMenu();
        $voiceMenu->setLabel(self::LABEL_ALPHA);
        $this->assertEquals(self::LABEL_ALPHA, $voiceMenu->getLabel());
        $voiceMenu->setLabel(self::LABEL_BRAVO);
        $this->assertEquals(self::LABEL_BRAVO, $voiceMenu->getLabel());
        $voiceMenu->setLabel();
        $this->assertNull($voiceMenu->getLabel());
    }
}
