<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Event;

class MemberSipPoolUpdated
{
    public string $memberId;

    public function __construct($memberId)
    {
        $this->memberId = $memberId;
    }
}
