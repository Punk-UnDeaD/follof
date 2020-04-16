<?php

declare(strict_types=1);

namespace App\Model\User\Service;

class SipPasswordHasher
{
    public function hash(string $password): string
    {
        return md5($password);
    }
}
