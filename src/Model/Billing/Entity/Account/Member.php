<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * Class Member.
 *
 * @ORM\Entity
 * @ORM\Table(name="billing_members")
 */
class Member implements AggregateRoot
{
    use EventsTrait;
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\Column(type="string", length=25, nullable=true))
     */
    private ?string $login = null;

    /**
     * @ORM\Column(type="string", length=128, nullable=true))
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private ?string $passwordHash = null;

    /**
     * @ORM\Column(type="billing_member_role", length=32)
     */
    private Role $role;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="members")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Team $team;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    /**
     * @var SipAccount[]|Collection
     * @ORM\OneToMany(targetEntity="App\Model\Billing\Entity\Account\SipAccount", mappedBy="member", orphanRemoval=true, cascade={"all"})
     */
    private Collection $sipAccounts;

    /**
     * Member constructor.
     */
    public function __construct(Team $team)
    {
        $this->id = Id::next();
        if ($team->getMembers()->count()) {
            $this->role = Role::member();
            $this->status = static::STATUS_BLOCKED;
        } else {
            $this->role = Role::owner();
            $this->status = static::STATUS_ACTIVE;
        }
        $this->team = $team;
        $team->addMember($this);
        $this->sipAccounts = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function removeCredentials(): self
    {
        $this->passwordHash = null;
        $this->login = null;

        return $this;
    }

    public function setCredentials(string $login, string $passwordHash): self
    {
        Assert::false($this->getRole()->isOwner(), 'Can\'t set owner credentials.');
        Assert::notEmpty($login, 'Can\'t set empty login.');
        Assert::notEmpty($passwordHash, 'Can\'t set wrong password hash.');
        $this->passwordHash = $passwordHash;
        if ($login === $this->login) {
            return $this;
        }
        foreach ($this->getTeam()->getMembers() as $member) {
            Assert::notEq($login, $member->getLogin(), 'Login already used in Team.');
        }
        $this->login = $login;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        Assert::true($this->role->isOwner());
        Assert::null($this->team, 'Cannot change team');
        $this->team = $team;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function activate(): self
    {
        Assert::false($this->isActive(), 'Already activated.');
        $this->status = static::STATUS_ACTIVE;
        $this->recordEvent(new Event\MemberSipPoolUpdated($this->getId()->getValue()));

        return $this;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function block(): self
    {
        Assert::true($this->isActive(), 'Already blocked.');
        Assert::false($this->role->isOwner(), 'Cannot block owner.');
        $this->status = static::STATUS_BLOCKED;
        $this->recordEvent(new Event\MemberSipPoolUpdated($this->getId()->getValue()));

        return $this;
    }

    public function addSipAccount(SipAccount $sipAccount): self
    {
        Assert::null($sipAccount->getMember(), 'Already added.');
        $this->sipAccounts->add($sipAccount);
        $this->recordEvent(new Event\MemberSipPoolUpdated($this->getId()->getValue()));

        return $this;
    }

    /**
     * @return SipAccount[]|Collection
     */
    public function getSipAccounts(): Collection
    {
        return $this->sipAccounts;
    }
}
