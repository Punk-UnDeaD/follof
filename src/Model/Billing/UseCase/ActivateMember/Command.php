<?php

namespace App\Model\Billing\UseCase\ActivateMember;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $member_id;

    public function __construct(string $member_id)
    {
        $this->member_id = $member_id;
    }
}
