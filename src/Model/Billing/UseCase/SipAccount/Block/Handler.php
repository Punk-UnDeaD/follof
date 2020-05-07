<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\Block;

use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\UseCase\SipAccount\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(SipAccount $sipAccount): void
    {
        $sipAccount->block();
    }
}
