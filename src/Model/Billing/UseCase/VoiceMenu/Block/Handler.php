<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Block;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(VoiceMenu $voiceMenu): void
    {
        $voiceMenu->block();
    }
}
