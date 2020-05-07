<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\UpdateLogin;

use App\Model\Billing\UseCase\SipAccount\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public string $login;

    public function __construct(string $id, string $login)
    {
        $this->id = $id;
        $this->login = $login;
    }
}
