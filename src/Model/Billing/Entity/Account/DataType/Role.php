<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\DataType;

use Webmozart\Assert\Assert;

class Role
{
    public const OWNER = 'ROLE_BILLING_TEAM_OWNER';
    public const ADMIN = 'ROLE_BILLING_TEAM_ADMIN';
    public const MEMBER = 'ROLE_BILLING_TEAM_MEMBER';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf(
            $name,
            [
                self::OWNER,
                self::ADMIN,
                self::MEMBER,
            ]
        );

        $this->name = $name;
    }

    public static function owner(): self
    {
        return new self(self::OWNER);
    }

    public static function admin(): self
    {
        return new self(self::ADMIN);
    }

    public static function member(): self
    {
        return new self(self::MEMBER);
    }

    public function isOwner(): bool
    {
        return self::OWNER === $this->name;
    }

    public function isAdmin(): bool
    {
        return self::ADMIN === $this->name || self::OWNER === $this->name;
    }

    public function isMember(): bool
    {
        return self::MEMBER === $this->name;
    }

    public function isEqual(self $role): bool
    {
        return $this->getName() === $role->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }
}
