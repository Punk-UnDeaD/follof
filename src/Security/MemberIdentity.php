<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Billing\Entity\Account\Member;
use App\Model\User\Entity\User\User;
use App\ReadModel\Billing\AuthView;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class MemberIdentity implements UserInterface, EquatableInterface
{
    private $id;
    private $username;
    private $password;
    private $display;
    private $role;
    private $user_status;
    private $member_status;

    public function __construct(AuthView $member)
    {
        $this->id = $member->id;
        $this->password = $member->password_hash;
        $this->username = $this->display = $member->login;
        $this->role = $member->role;
        $this->user_status = $member->user_status;
        $this->member_status = $member->member_status;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->user_status === User::STATUS_ACTIVE
            && $this->member_status === Member::STATUS_ACTIVE;
    }

    public function getDisplay(): string
    {
        return $this->display;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [$this->role, 'ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {

    }

    public function isEqualTo(UserInterface $member): bool
    {
        if (!$member instanceof self) {
            return false;
        }

        return
            $this->id === $member->id &&
            $this->password === $member->password &&
            $this->role === $member->role &&
            $this->user_status === $member->user_status &&
            $this->member_status === $member->member_status;
    }
}
