<?php

declare(strict_types=1);

namespace App\Model\User\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 */
class SipAccount
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var string
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    private $id;
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="networks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;
    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private string $login;

    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private string $password;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    public function __construct(User $user, string $login, string $password)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->user = $user;
        $this->login = $login;
        $this->password = $password;
        $this->status = self::STATUS_BLOCKED;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new \DomainException('Sip account is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new \DomainException('Sip account is already blocked.');
        }
        $this->status = self::STATUS_BLOCKED;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function isSameLogin($login): bool
    {
        return $this->login === $login;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

}
