<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\Billing\Entity\Account\DataType\Id;
use App\Model\Billing\Entity\Account\Field\IdTrait;
use Webmozart\Assert\Assert;

class PhoneNumber
{
    use IdTrait;

    public const FORMAT = '/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}?$/';

    private string $number;

    public function __construct(string $number)
    {
        Assert::regex($number, static::FORMAT, 'Wrong format.');
        $this->number = $number;
        $this->id = Id::next();
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
