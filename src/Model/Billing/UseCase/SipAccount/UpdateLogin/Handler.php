<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\UpdateLogin;

use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\UseCase\SipAccount\BaseHandlerTrait;

class Handler
{
    use BaseHandlerTrait;

    protected function handle(SipAccount $sipAccount, Command $command): void
    {
        $sipAccount->setLogin($command->login);
    }
}
