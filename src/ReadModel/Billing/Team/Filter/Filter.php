<?php

declare(strict_types=1);

namespace App\ReadModel\Billing\Team\Filter;

class Filter
{
    public ?string $billingId = null;
    public ?float $balanceMin = null;
    public ?float $balanceMax = null;
    public ?string $email = null;
}
