<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\AddPoint;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(VoiceMenu $voiceMenu, Command $command): void
    {
        $numbers = array_map(fn ($number) => new InternalNumber(trim($number)), explode(',', $command->number));
        $voiceMenu->addPoint($command->key, ...$numbers);
    }
}
