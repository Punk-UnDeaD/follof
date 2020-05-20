<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Field;

use App\Model\Billing\Entity\Account\SipAccount;
use Webmozart\Assert\Assert;

trait StatusTrait
{
    /**
     * @ORM\Column(type="string", length=16)
     */
    protected string $status;

    public function activate(): self
    {
        Assert::true($this->isActivated(), 'Can\'t activate.');
        Assert::false($this->isActive(), 'Already activated.');
        $this->status = static::STATUS_ACTIVE;

        return $this->onUpdateStatus();
    }

    abstract public function isActivated(): bool;

    public function isActive(): bool
    {
        return static::STATUS_ACTIVE === $this->status;
    }

    abstract protected function onUpdateStatus(): self;

    public function block(): self
    {
        Assert::true($this->isActive(), 'Already blocked.');
        $this->status = static::STATUS_BLOCKED;

        return $this->onUpdateStatus();
    }

    public function isBlocked(): bool
    {
        return SipAccount::STATUS_BLOCKED === $this->status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
