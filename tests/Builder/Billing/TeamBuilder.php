<?php


namespace App\Tests\Builder\Billing;


use App\Model\Billing\Entity\Account\Member;
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

    public function __construct()
    {
        $this->user = (new UserBuilder())->viaEmail()->build()->activate();
        $this->owner = Member::createFromUser($this->user);
        $this->team = new Team($this->owner, '--');
        $this->member = Member::createTeamMember($this->team);
    }

    /**
     * @return array
     */
    public function getAll():array
    {
        return [
            'user' => $this->user,
            'owner' => $this->owner,
            'team' => $this->team,
            'member' => $this->member,
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
}