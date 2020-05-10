<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use Webmozart\Assert\Assert;

class PhoneNumber
{
    public const FORMAT = '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}(?>,\d{1,5})?$/';

    private ?string $number = null;

    public function __construct(string $number)
    {
        Assert::regex($number, static::FORMAT, 'Wrong format.');
        $this->number = $number;
    }

    public function isSame(self $other): bool
    {
        return $this->number === $other->number;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function __toString()
    {
        return $this->number;
    }
}
