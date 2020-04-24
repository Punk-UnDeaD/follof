<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\BlockSipAccount;

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
