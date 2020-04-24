<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Network\Attach;

class Command
{
    public string $user;

    public string $network;

    public string $identity;

    public function __construct(string $user, string $network, string $identity)
    {
        $this->user = $user;
        $this->network = $network;
        $this->identity = $identity;
    }
}
