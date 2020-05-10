<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Team\AddMember;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Model\Billing\UseCase\Team\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(Team $team): void
    {
        new Member($team);
    }
}
