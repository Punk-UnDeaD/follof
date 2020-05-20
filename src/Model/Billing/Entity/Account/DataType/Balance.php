<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\DataType;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Balance
{
    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private float $value = 0.0;

    public function getValue(): float
    {
        return $this->value;
    }

    public function isNegative()
    {
        return $this->value < 0;
    }

    public function __toString(): string
    {
        return number_format($this->value, 2, '.', '');
    }
}
