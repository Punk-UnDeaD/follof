<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Team\AddVoiceMenu;

use App\Model\Billing\Entity\Account\Team;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\Team\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(Team $team): void
    {
        new VoiceMenu($team);
    }
}
