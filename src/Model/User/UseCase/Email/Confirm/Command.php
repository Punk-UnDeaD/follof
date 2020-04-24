<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Email\Confirm;

class Command
{
    public string $id;

    public string $token;

    public function __construct(string $id, string $token)
    {
        $this->id = $id;
        $this->token = $token;
    }
}
