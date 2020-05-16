<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\SetFallbackNumber;

use App\Model\Billing\Entity\Account\InternalNumber;
use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\UseCase\Member\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(Member $member, Command $command): void
    {
        $member->setFallbackNumber(new InternalNumber($command->number));
    }
}
