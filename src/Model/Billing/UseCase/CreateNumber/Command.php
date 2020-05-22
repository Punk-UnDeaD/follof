<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\CreateNumber;

class Command
{
    public string $number;

    public function __construct(string $number)
    {
        $this->number = $number;
    }
}
