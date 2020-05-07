<?php

namespace App\Model\Billing\UseCase\Member\UpdateCredentials;

use App\Model\Billing\UseCase\Member\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public ?string $login;
    public ?string $password;

    public function __construct(string $id, ?string $login, ?string $password)
    {
        $this->id = $id;
        $this->login = $login;
        $this->password = $password;
    }
}
