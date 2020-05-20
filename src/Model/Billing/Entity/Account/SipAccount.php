<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account;

use App\Model\AggregateRoot;
use App\Model\Billing\Entity\Account\DataType\Id;
use App\Model\Billing\Entity\Account\Field\DataTrait;
use App\Model\Billing\Entity\Account\Field\LabelTrait;
use App\Model\Billing\Entity\Account\Field\StatusTrait;
use App\Model\Billing\Entity\Account\Field\WaitTimeTrait;
use App\Model\EventsTrait;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="billing_sip_accounts")
 */
class SipAccount implements AggregateRoot
{
    use EventsTrait,
        DataTrait,
        LabelTrait,
        WaitTimeTrait,
        StatusTrait {
        DataTrait::checkData insteadof WaitTimeTrait;
        DataTrait::checkData insteadof LabelTrait;
    }

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * @ORM\Column(type="billing_guid")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="string", length=32)
     */
    private ?string $login = null;
    /**
     * @ORM\Column(type="string", length=128)
     */
    private string $password;

    /**
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
        $this->data = static::defaultData();
        $this->setLabel('sip-'.$member->getSipAccounts()->count());
    }

    public static function defaultData(): array
    {
        return LabelTrait::defaultData() + WaitTimeTrait::defaultData();
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        Assert::notEmpty($login, 'Can\'t set empty login.');
        if ($this->login === $login) {
            return $this;
        }
        foreach ($this->getMember()->getTeam()->getMembers() as $member) {
            foreach ($member->getSipAccounts() as $sipAccount) {
                Assert::notSame($login, $sipAccount->getLogin(), 'Login already used.');
            }
        }

        $this->login = $login;

        if ($this->isActive()) {
            $this->recordEvent(new Event\MemberDataUpdated($this->getMember()->getId()->getValue()));
        }

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        Assert::notEmpty($password, 'Can\'t set empty password.');
        $this->password = $password;

        if ($this->isActive()) {
            $this->recordEvent(new Event\MemberDataUpdated($this->getMember()->getId()->getValue()));
        }

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function isSameLogin($login): bool
    {
        return $this->login === $login;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    protected function onUpdateWaitTime(): self
    {
        return  $this->recordEvent(new Event\MemberDataUpdated($this->getMember()->getId()->getValue()));
    }

    public function isActivated(): bool
    {
        return true;
    }

    protected function onUpdateStatus(): self
    {
        return $this->recordEvent(new Event\MemberDataUpdated($this->getMember()->getId()->getValue()));
    }
}
