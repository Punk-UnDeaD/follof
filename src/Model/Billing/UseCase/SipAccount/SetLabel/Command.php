<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\SetLabel;

use App\Model\Billing\UseCase\SipAccount\BaseCommandTrait;

class Command
{
    use BaseCommandTrait;

    public ?string $label;

    public function __construct(string $id, ?string $label)
    {
        $this->id = $id;
        $this->label = $label;
    }
}
