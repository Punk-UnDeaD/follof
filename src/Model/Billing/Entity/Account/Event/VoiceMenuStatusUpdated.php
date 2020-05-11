<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Event;

class VoiceMenuStatusUpdated
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
