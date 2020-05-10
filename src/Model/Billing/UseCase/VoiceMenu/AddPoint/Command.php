<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\AddPoint;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public string $key;
    public string $number;

    public function __construct(string $id, string $key, string $number)
    {
        $this->key = $key;
        $this->number = $number;
        $this->id = $id;
    }
}
