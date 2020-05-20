<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\SetWaitTime;

use App\Model\Billing\UseCase\SipAccount\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public ?int $waitTime;

    public function __construct(string $id, ?int $waitTime)
    {
        $this->id = $id;
        $this->waitTime = $waitTime;
    }
}
