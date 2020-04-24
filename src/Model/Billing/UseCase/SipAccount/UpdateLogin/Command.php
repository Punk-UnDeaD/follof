<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\UpdateLogin;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $sipAccount_id;
    public string $login;

    public function __construct(string $sipAccount_id, string $login)
    {
        $this->sipAccount_id = $sipAccount_id;
        $this->login = $login;
    }
}
