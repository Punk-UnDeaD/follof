<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\SetFallbackNumber;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public string $number;

    public function __construct(string $id, string $number)
    {
        $this->id = $id;
        $this->number = $number;
    }
}
