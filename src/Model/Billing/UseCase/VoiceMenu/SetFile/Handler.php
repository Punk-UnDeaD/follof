<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\SetFile;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait {
        __construct as baseConstruct;
    }

    protected function handle(VoiceMenu $voiceMenu, Command $command): void
    {
        $file = $voiceMenu->getFile();
        if (is_file($file)) {
            unlink($file);
        }
        $voiceMenu->setFile($command->file);
    }
}
