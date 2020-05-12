<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\SetLabel;

use App\Model\Billing\UseCase\Member\BaseCommandTrait;

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
