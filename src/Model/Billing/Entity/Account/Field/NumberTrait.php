<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

use App\Model\Billing\Entity\Account\Number;
use Webmozart\Assert\Assert;

trait NumberTrait
{
    /**
     * @ORM\OneToOne(targetEntity="Number")
     * @ORM\JoinColumn(name="number", referencedColumnName="number", nullable=true, onDelete="SET NULL")
     */
    private ?Number $number = null;

    public function getNumber(): ?Number
    {
        return $this->number;
    }

    public function setNumber(?Number $number = null): self
    {
        if ($this->number === $number) {
            return $this;
        }
        if ($number) {
            Assert::same($this->getTeam(), $number->getTeam(), 'Team can\'t contain this Number.');
            Assert::true($this->getTeam()->isNumberFree($number), 'Number not free.');
        }

        $this->number = $number;
        $this->onUpdateNumber();

        return $this;
    }

    abstract protected function onUpdateNumber();
}
