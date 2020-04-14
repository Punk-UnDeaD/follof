<?php


namespace App\Model\User\UseCase\SipAccount\Add;


class Command
{
    /**
     * @var string
     */
    public $user;
    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;

    public function __construct(string $user, string $login, string $password)
    {
        $this->user = $user;
        $this->login = $login;
        $this->password = $password;
    }
}
