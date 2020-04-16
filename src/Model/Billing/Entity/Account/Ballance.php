<?php


namespace App\Model\Billing\Entity\Account;


class Ballance
{
    private float $value = 0.0;

    /**
     * @return float|int
     */
    public function getValue():float
    {
        return $this->value;
    }

    public function isNegative()
    {
        return $this->value < 0;
    }
}