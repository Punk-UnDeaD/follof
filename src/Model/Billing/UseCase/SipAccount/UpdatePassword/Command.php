<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\UpdatePassword;

use App\Model\Billing\UseCase\SipAccount\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public string $password;

    public function __construct(string $id, string $password)
    {
        $this->id = $id;
        $this->password = $password;
    }
}
