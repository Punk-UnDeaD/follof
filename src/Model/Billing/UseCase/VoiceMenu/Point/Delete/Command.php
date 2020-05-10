<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Point\Delete;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public string $key;

    public function __construct(string $id, string $key)
    {
        $this->id = $id;
        $this->key = $key;
    }
}
