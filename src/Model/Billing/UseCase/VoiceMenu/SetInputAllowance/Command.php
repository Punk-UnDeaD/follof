<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\SetInputAllowance;

use App\Model\Billing\UseCase\VoiceMenu\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public bool $allowance;

    public function __construct(string $id, bool $allowance)
    {
        $this->id = $id;
        $this->allowance = $allowance;
    }
}
