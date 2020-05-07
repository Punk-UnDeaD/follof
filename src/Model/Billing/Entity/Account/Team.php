<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
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
    use EventsTrait;
    /**
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;

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
     * @ORM\Embedded(class="Ballance")
     */
    private Ballance $ballance;

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
        $this->owner = new Member($this);
        $this->id = Id::next();
        $this->ballance = new Ballance();
        $this->billingId = $billingId;
        $this->user = $user;
    }

    public function getOwner(): Member
    {
        return $this->owner;
    }

    /**
     * @return Member[]|Collection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        Assert::eq($this, $member->getTeam(), '$member in other Team.');
        Assert::False($this->members->contains($member), '$member already added.');
        $this->members->add($member);

        return $this;
    }

    public function removeMember(Member $member): self
    {
        Assert::False($member->getRole()->isOwner(), 'Can\'t remove owner.');
        Assert::eq($this, $member->getTeam(), '$member in other Team.');
        Assert::true($this->members->contains($member), '$member already not in Team.');
        $this->members->removeElement($member);

        return $this;
    }

    public function getBillingId(): string
    {
        return $this->billingId;
    }

    public function getBallance(): Ballance
    {
        return $this->ballance;
    }

    public function getId(): Id
    {
        return $this->id;
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

    public function checkInternalNumberFor(InternalNumber $number, HasNumber $entity): bool
    {
        foreach ($this->getVoiceMenus() as $voiceMenu) {
            if ($voiceMenu->getInternalNumber()->isSame($number)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return VoiceMenu[]
     */
    public function getVoiceMenus(): Collection
    {
        return $this->voiceMenus;
    }
}
