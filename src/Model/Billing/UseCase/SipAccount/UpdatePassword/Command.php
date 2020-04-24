<?php


namespace App\Model\Billing\UseCase\SipAccount\UpdatePassword;


class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $sipAccount_id;
    public string $password;

    public function __construct(string $sipAccount_id, string $password)
    {
        $this->sipAccount_id = $sipAccount_id;
        $this->password = $password;
    }
}