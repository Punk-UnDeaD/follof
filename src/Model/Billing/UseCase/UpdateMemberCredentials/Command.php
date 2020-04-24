<?php


namespace App\Model\Billing\UseCase\UpdateMemberCredentials;


class Command
{
    public string $member_id;
    public ?string $login;
    public ?string $password;

    public function __construct(string $member_id, ?string $login, ?string $password)
    {
        $this->member_id = $member_id;
        $this->login = $login;
        $this->password = $password;
    }
}