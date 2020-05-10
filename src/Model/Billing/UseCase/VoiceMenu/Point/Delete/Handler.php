<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Point\Delete;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(VoiceMenu $voiceMenu, Command $command): void
    {
        $voiceMenu->removePoint($command->key);
    }
}
