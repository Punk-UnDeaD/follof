<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="billing_sip_accounts")
 */
class SipAccount
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var Id
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private ?string $login = null;
    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    private string $password;
    /**
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private string $status;

    /**
     * @var Member
     * @ORM\ManyToOne(targetEntity="App\Model\Billing\Entity\Account\Member", inversedBy="sipAccounts")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?Member $member = null;

    public function __construct(Member $member, string $login, string $password)
    {
        $this->id = Id::next();
        $this->password = $password;
        $this->status = self::STATUS_BLOCKED;
        $member->addSipAccount($this);
        $this->member = $member;
        $this->setLogin($login);
    }

    /**
     * @return string
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function activate(): self
    {
        Assert::false($this->isActive(), 'Sip account is already active.');
        $this->status = self::STATUS_ACTIVE;

        return $this;
    }

    public function block(): self
    {
        Assert::false($this->isBlocked(), 'Sip account is already blocked.');
        $this->status = self::STATUS_BLOCKED;

        return $this;
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Member
     */
    public function getMember(): ?Member
    {
        return $this->member;
    }

    /**
     * @param string $password
     * @return SipAccount
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        foreach ($this->getMember()->getTeam()->getMembers() as $member) {
            foreach ($member->getSipAccounts() as $sipAccount) {
                Assert::notSame($login, $sipAccount->getLogin(), 'Login already used.');
            }
        }

        $this->login = $login;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

}
