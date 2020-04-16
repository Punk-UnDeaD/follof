<?php


namespace App\Model\Billing\Entity\Account;


use Doctrine\Common\Collections\ArrayCollection;
use Webmozart\Assert\Assert;

class Team
{
    private Id $id;

    private string $billingId;

    private Member $owner;

    /**
     * @var ArrayCollection
     */
    private ArrayCollection $members;

    private Ballance $ballance;
    /**
     * Team constructor.
     * @param Member $owner
     * @param string $billingId
     */
    public function __construct(Member $owner, string $billingId)
    {
        Assert::true($owner->getRole()->isOwner());
        Assert::notEmpty($billingId, 'Cannot create. Empty $billingId.');
        Assert::null($owner->getTeam(), 'Cannot create. $owner has team.');
        $this->id = Id::next();
        $this->owner = $owner;
        $owner->setTeam($this);
        $this->ballance = new Ballance();
        $this->members = new ArrayCollection([$owner]);
        $this->billingId = $billingId;
    }

    /**
     * @return Member
     */
    public function getOwner(): Member
    {
        return $this->owner;
    }

    /**
     * @return Member[]
     */
    public function getMembers(): array
    {
        return $this->members->toArray();
    }

    public function addMember(Member $member): self
    {
        Assert::False($member->getRole()->isOwner(), '$member can\'t be owner.');
        Assert::eq($this, $member->getTeam(), '$member in other Team.');
        Assert::False($this->members->indexOf($member), '$member already added.');
        $this->members->add($member);

        return $this;
    }

    public function removeMember(Member $member): self
    {
        Assert::False($member->getRole()->isOwner(), 'Can\'t remove owner.');
        Assert::eq($this, $member->getTeam(), '$member in other Team.');
        Assert::integer($this->members->indexOf($member), '$member already not in Team.');
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

}