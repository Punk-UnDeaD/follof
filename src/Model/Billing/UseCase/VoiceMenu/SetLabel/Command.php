<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\SetLabel;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public ?string $label;

    public function __construct(string $id, ?string $label = null)
    {
        $this->id = $id;
        $this->label = $label;
    }
}
