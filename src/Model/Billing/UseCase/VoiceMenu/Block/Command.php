<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Block;

class Command
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
