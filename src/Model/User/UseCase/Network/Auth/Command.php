<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Auth;

class Command
{
    public string $network;

    public string $identity;

    public string $firstName;

    public string $lastName;

    public function __construct(string $network, string $identity)
    {
        $this->network = $network;
        $this->identity = $identity;
    }
}
