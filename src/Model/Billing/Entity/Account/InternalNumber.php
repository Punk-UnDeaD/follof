<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class InternalNumber implements \JsonSerializable
{
    public const FORMAT = '/^\d(?>-?\d){0,4}?$/';

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $number;

    public function __construct(string $number)
    {
        Assert::regex($number, static::FORMAT, 'Wrong format.');
        $this->number = $number;
    }

    public function isSame(self $other): bool
    {
        return $this->getCleanValue() === $other->getCleanValue();
    }

    public function getCleanValue()
    {
        return str_replace('-', '', $this->number);
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function __toString()
    {
        return $this->number;
    }

    public function jsonSerialize()
    {
        return $this->number;
    }
}
