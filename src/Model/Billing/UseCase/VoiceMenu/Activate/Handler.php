<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Activate;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\AbstractHandler;

class Handler extends AbstractHandler
{
    protected function handle(VoiceMenu $voiceMenu): void
    {
        $voiceMenu->activate();
    }
}
