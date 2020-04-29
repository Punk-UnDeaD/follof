<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\CreateTeam;

class Command
{
    public string $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }
}
