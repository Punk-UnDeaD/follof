<?php

namespace App\Model\Billing\Entity\Account;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Member
 * @package App\Model\Billing\Entity\Account
 *
 * @ORM\Entity
 * @ORM\Table(name="billing_members")
 *
 */
class Member
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @var Id
     * @ORM\Column(type="billing_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=25, nullable=true))
     */
    private ?string $login = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=128, nullable=true))
     */
    private ?string $email = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="password_hash", nullable=true)
     */
    private ?string $passwordHash = null;

    /**
     * @var Role
     * @ORM\Column(type="billing_member_role", length=32)
     */
    private Role $role;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="members")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Team $team;

    /**
     * @var string
     * @ORM\Column(type="string", length=16)
     */
    private string $status;

    /**
     * Member constructor.
     * @param Team $team
     */
    public function __construct(Team $team)
    {
        $this->id = Id::next();
        if ($team->getMembers()->count()) {
            $this->role = Role::member();
        } else {
            $this->role = Role::owner();
        }
        $this->team = $team;
        $this->status = static::STATUS_ACTIVE;
        $team->addMember($this);
    }

    /**
     * @return Team|NULL
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @param Team $team
     * @return Member
     */
    public function setTeam(Team $team): self
    {
        Assert::true($this->role->isOwner());
        Assert::null($this->team, 'Cannot change team');
        $this->team = $team;

        return $this;
    }

    /**
     * @return Role
     */
    public function getRole(): Role
    {
        return $this->role;
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
            Assert::notEq($login, $member->getLogin(), '$login already used in Team.');
        }
        $this->login = $login;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function isActive(): bool
    {
        return $this->status === static::STATUS_ACTIVE;
    }

    public function activate(): self
    {
        Assert::false($this->isActive(), 'Already activated.');
        $this->status = static::STATUS_ACTIVE;

        return $this;
    }

    public function block(): self
    {
        Assert::true($this->isActive(), 'Already blocked.');
        Assert::false($this->role->isOwner(), 'Cannot block owner.');
        $this->status = static::STATUS_BLOCKED;

        return $this;
    }


}