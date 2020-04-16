<?php


namespace App\Model\Billing\Entity\Account;


use App\Model\User\Entity\User\User;
use Webmozart\Assert\Assert;

class Member
{
    private Id $id;

    private ?string $login = null;

    private ?string $email = null;

    private ?string $passwordHash = null;

    private Role $role;

    private ?Team $team = null;

    /**
     * Member constructor.
     * @param Id $id
     */
    private function __construct(Id $id)
    {
        $this->id = $id;
    }

    static public function createFromUser(User $user)
    {
        Assert::true($user->isActive(), 'Cannot create. User not active.');
        Assert::true($user->getRole()->isUser(), 'Cannot create. Wrong role.');
        $member = new self(Id::next());
        $member->email = $user->getEmail()->getValue();
        $member->role = Role::owner();

        return $member;
    }

    static public function createTeamMember(Team $team)
    {
        $member = new self(Id::next());
        $member->role = Role::member();
        $member->team = $team;
        $team->addMember($member);

        return $member;
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

}