<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id implements \JsonSerializable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid6()->toString());
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
