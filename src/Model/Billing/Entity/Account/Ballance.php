<?php


namespace App\Model\Billing\Entity\Account;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Ballance
{
    /**
     * @var float
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private float $value = 0.0;

    /**
     * @return float|int
     */
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