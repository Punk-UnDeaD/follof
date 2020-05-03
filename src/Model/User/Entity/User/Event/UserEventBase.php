<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User\Event;

abstract class UserEventBase
{
    public string $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }
}
