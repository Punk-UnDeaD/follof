<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Number\SetTeam;

use App\Model\Billing\UseCase\Number\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public ?string $teamBillingId;

    public function __construct(string $number, ?string $teamBillingId = null)
    {
        $this->number = $number;
        $this->teamBillingId = $teamBillingId;
    }
}
