<?php

namespace App\Model\Billing\Entity\Account;

use App\Model\User\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Team
 * @package App\Model\Billing\Entity\Account
 * @ORM\Entity
 * @ORM\Table(name="billing_team")
 */
class Team
{
    /**
     * @var Id
     * @ORM\Column(type="billing_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="billing_id")
     */
    private string $billingId;

    /**
     * @var Member
     * @ORM\OneToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private Member $owner;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",  nullable=false)
     */
    private User $user;


    /**
     * @var Member[]|Collection
     * @ORM\OneToMany(targetEntity="Member", mappedBy="team", orphanRemoval=true, cascade={"all"})
     */
    private $members;


    /**
     * @var Ballance
     * @ORM\Embedded(class="Ballance")
     */
    private Ballance $ballance;

    /**
     * Team constructor.
     * @param User $user
     * @param string $billingId
     */
    public function __construct(User $user, string $billingId)
    {
        Assert::true($user->getRole()->isUser(), 'Cannot create. Wrong user role.');
        Assert::true($user->isActive(), 'Cannot create. User not active.');
        Assert::notEmpty($billingId, 'Cannot create. Empty $billingId.');
        $this->members = new ArrayCollection();
        $this->owner = new Member($this);
        $this->id = Id::next();
        $this->ballance = new Ballance();
        $this->billingId = $billingId;
        $this->user = $user;
    }

    /**
     * @return Member
     */
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


    /**
     * @return string
     */
    public function getBillingId(): string
    {
        return $this->billingId;
    }

    /**
     * @return Ballance
     */
    public function getBallance(): Ballance
    {
        return $this->ballance;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

}