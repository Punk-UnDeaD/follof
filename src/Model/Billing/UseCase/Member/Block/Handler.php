<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\Block;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\UseCase\Member\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(Member $member): void
    {
        $member->block();
    }
}
