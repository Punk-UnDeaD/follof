<?php

namespace App\Model\Billing\UseCase\ActivateSipAccount;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $sipAccount_id;

    public function __construct(string $sipAccount_id)
    {
        $this->sipAccount_id = $sipAccount_id;
    }
}
