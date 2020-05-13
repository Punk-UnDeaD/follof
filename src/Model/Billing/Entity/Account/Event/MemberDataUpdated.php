<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Event;

class MemberDataUpdated
{
    public string $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}
