<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use Webmozart\Assert\Assert;

trait InternalNumberTrait
{
    /**
     * @ORM\Embedded(class="App\Model\Billing\Entity\Account\DataType\InternalNumber")
     */
    protected ?InternalNumber $internalNumber = null;

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbedsInternalNumber(): void
    {
        if ($this->internalNumber->isNull()) {
            $this->internalNumber = null;
        }
    }

    public function getInternalNumber(): ?InternalNumber
    {
        return $this->internalNumber;
    }

    public function setInternalNumber(InternalNumber $internalNumber): self
    {
        if (!$this->internalNumber || !$this->internalNumber->isSame($internalNumber)) {
            Assert::true(($this->getTeam())->checkInternalNumberFor($internalNumber), 'Number can\'t be used.');
        }
        $this->internalNumber = $internalNumber;
        $this->onUpdateInternalNumber();

        return $this;
    }

    abstract protected function onUpdateInternalNumber();
}
