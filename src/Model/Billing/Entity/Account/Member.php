<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\Billing\Entity\Account\DataType\Id;
use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use App\Model\Billing\Entity\Account\DataType\Role;
use App\Model\Billing\Entity\Account\Field\DataTrait;
use App\Model\Billing\Entity\Account\Field\IdTrait;
use App\Model\Billing\Entity\Account\Field\InternalNumberTrait;
use App\Model\Billing\Entity\Account\Field\LabelTrait;
use App\Model\Billing\Entity\Account\Field\StatusTrait;
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
 * @ORM\HasLifecycleCallbacks
 */
class Member implements AggregateRoot
{
    use IdTrait;
    use EventsTrait;
    use InternalNumberTrait;
    use DataTrait,
        LabelTrait,
        StatusTrait {
        DataTrait::checkData as protected baseCheckData;
        StatusTrait::block as protected baseBlock;
    }

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

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
    private ?Team $team = null;
    /**
     * @var SipAccount[]|Collection
     * @ORM\OneToMany(targetEntity="App\Model\Billing\Entity\Account\SipAccount", mappedBy="member", orphanRemoval=true, cascade={"all"})
     */
    private Collection $sipAccounts;

    public function __construct(Team $team)
    {
        $this->id = Id::next();
        $this->data = static::defaultData();
        $team->addMember($this);
        $this->team = $team;
        if (1 === $team->getMembers()->count()) {
            $this->role = Role::owner();
            $this->status = static::STATUS_ACTIVE;
            $this->setLabel('owner');
            $this->setInternalNumber(new InternalNumber('000'));
        } else {
            $this->role = Role::member();
            $this->status = static::STATUS_BLOCKED;
            $this->setLabel('member-'.count($team->getMembers()));
        }
        $this->sipAccounts = new ArrayCollection();
    }

    protected static function defaultData(): array
    {
        return LabelTrait::defaultData() + ['fallbackNumber' => null];
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

    public function isActivated(): bool
    {
        return (bool) $this->internalNumber && (bool) $this->sipAccounts->count();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function block(): self
    {
        Assert::false($this->role->isOwner(), 'Cannot block owner.');

        return $this->baseBlock();
    }

    public function addSipAccount(SipAccount $sipAccount): self
    {
        Assert::null($sipAccount->getMember(), 'Already added.');
        $this->sipAccounts->add($sipAccount);
        $this->recordEvent(new Event\MemberDataUpdated($this->getId()->getValue()));

        return $this;
    }

    /**
     * @return SipAccount[]|Collection
     */
    public function getSipAccounts(): Collection
    {
        return $this->sipAccounts;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkData(): void
    {
        $this->baseCheckData();
        if ($this->data['fallbackNumber']) {
            $this->data['fallbackNumber'] = new InternalNumber($this->data['fallbackNumber']);
        }
    }

    public function getFallbackNumber(): ?InternalNumber
    {
        return $this->data['fallbackNumber'];
    }

    public function setFallbackNumber(?InternalNumber $number): self
    {
        $this->data['fallbackNumber'] = $number;
        $this->recordEvent(new Event\MemberDataUpdated($this->getId()->getValue()));

        return $this;
    }

    protected function onUpdateInternalNumber()
    {
        $this->recordEvent(new Event\MemberDataUpdated($this->getId()->getValue()));
    }

    protected function onUpdateStatus(): self
    {
        return $this->recordEvent(new Event\MemberStatusUpdated($this->getId()->getValue()));
    }
}
