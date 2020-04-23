<?php


namespace App\Tests\Builder\Billing;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\Entity\Account\Team;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\User\UserBuilder;

class TeamBuilder
{
    private User $user;
    /**
     * @var Member
     */
    private Member $owner;
    /**
     * @var Team
     */
    private Team $team;
    /**
     * @var Member
     */
    private Member $member;
    /**
     * @var SipAccount
     */
    private SipAccount $sipAccount;

    public function __construct()
    {
        $this->user = (new UserBuilder())->viaEmail()->build()->activate();
        $this->team = new Team($this->user, '--');
        $this->owner = $this->team->getOwner();
        $this->member = new Member($this->team);
        $this->sipAccount = new SipAccount($this->member, '~~', '~~');
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return [
            'user' => $this->user,
            'owner' => $this->owner,
            'team' => $this->team,
            'member' => $this->member,
            'sipAccount' =>$this->sipAccount,
        ];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Member
     */
    public function getOwner(): Member
    {
        return $this->owner;
    }

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @return SipAccount
     */
    public function getSipAccount(): SipAccount
    {
        return $this->sipAccount;
    }
}