<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\Billing\Entity\Account\DataType\Balance;
use App\Model\Billing\Entity\Account\DataType\Id;
use App\Model\Billing\Entity\Account\DataType\InternalNumber;
use App\Model\Billing\Entity\Account\Field\IdTrait;
use App\Model\EventsTrait;
use App\Model\User\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="billing_team")
 */
class Team implements AggregateRoot
{
    use IdTrait;

    use EventsTrait;

    /**
     * @ORM\Column(type="string", name="billing_id")
     */
    private string $billingId;

    /**
     * @ORM\OneToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private Member $owner;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @var Member[]|Collection
     * @ORM\OneToMany(targetEntity="Member", mappedBy="team", orphanRemoval=true, cascade={"all"})
     */
    private Collection $members;

    /**
     * @var VoiceMenu[]|Collection
     * @ORM\OneToMany(targetEntity="VoiceMenu", mappedBy="team", orphanRemoval=true, cascade={"all"})
     */
    private Collection $voiceMenus;

    /**
     * @var Number[]|Collection
     * @ORM\OneToMany(targetEntity="Number", mappedBy="team")
     */
    private Collection $numbers;

    /**
     * @ORM\Embedded(class="App\Model\Billing\Entity\Account\DataType\Balance")
     */
    private Balance $balance;

    /**
     * Team constructor.
     */
    public function __construct(User $user, string $billingId)
    {
        Assert::true($user->getRole()->isUser(), 'Cannot create. Wrong user role.');
        Assert::true($user->isActive(), 'Cannot create. User not active.');
        Assert::notEmpty($billingId, 'Cannot create. Empty $billingId.');
        $this->members = new ArrayCollection();
        $this->voiceMenus = new ArrayCollection();
        $this->numbers = new ArrayCollection();
        $this->owner = new Member($this);
        $this->id = Id::next();
        $this->balance = new Balance();
        $this->billingId = $billingId;
        $this->user = $user;
    }

    public function getOwner(): Member
    {
        return $this->owner;
    }

    public function addMember(Member $member): self
    {
        Assert::null($member->getTeam(), 'Already added.');
        $this->members->add($member);

        return $this;
    }

    public function getBillingId(): string
    {
        return $this->billingId;
    }

    public function getBalance(): Balance
    {
        return $this->balance;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function addVoiceMenu(VoiceMenu $voiceMenu): self
    {
        Assert::null($voiceMenu->getTeam(), 'Already added.');
        $this->voiceMenus->add($voiceMenu);

        return $this;
    }

    public function checkInternalNumberFor(InternalNumber $number): bool
    {
        foreach ($this->getVoiceMenus() as $voiceMenu) {
            if ($voiceMenu->getInternalNumber() && $voiceMenu->getInternalNumber()->isSame($number)) {
                return false;
            }
        }
        foreach ($this->getMembers() as $member) {
            if ($member->getInternalNumber() && $member->getInternalNumber()->isSame($number)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return VoiceMenu[]|Collection
     */
    public function getVoiceMenus(): Collection
    {
        return $this->voiceMenus;
    }

    /**
     * @return Member[]|Collection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function isNumberFree(Number $number)
    {
        Assert::same($this, $number->getTeam(), 'Team can\'t contain this Number.');

        foreach ($this->getVoiceMenus() as $voiceMenu) {
            if ($number === $voiceMenu->getNumber()) {
                return false;
            }
        }
        foreach ($this->getMembers() as $member) {
            if ($number === $member->getNumber()) {
                return false;
            }
        }

        return true;
    }

    public function getFreeNumbers(): array
    {
        $numbers = $this->numbers->toArray();
        $numbers = array_diff($numbers, array_map(fn ($e) => $e->getNumber(), $this->getVoiceMenus()->toArray()));
        $numbers = array_diff($numbers, array_map(fn ($e) => $e->getNumber(), $this->getMembers()->toArray()));

        return $numbers;
    }

    /**
     * @return Number[]|Collection
     */
    public function getNumbers(): Collection
    {
        return $this->numbers;
    }

    public function addNumber(Number $number): self
    {
        Assert::same($this, $number->getTeam(), 'Number not for team.');
        Assert::false($this->numbers->contains($number), 'Already contains.');
        $this->numbers->add($number);

        return $this;
    }

    public function removeNumber(Number $number): self
    {
        Assert::null($number->getTeam(), 'Can\'t remove.');
        Assert::true($this->numbers->contains($number), 'Already not contains.');
        $this->numbers->removeElement($number);

        foreach ($this->getVoiceMenus() as $voiceMenu) {
            if ($number === $voiceMenu->getNumber()) {
                $this->recordEvent(...$voiceMenu->setNumber()->releaseEvents());
                break;
            }
        }
        foreach ($this->getMembers() as $member) {
            if ($number === $member->getNumber()) {
                $this->recordEvent(...$member->setNumber()->releaseEvents());
                break;
            }
        }

        return $this;
    }
}
