<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\BlockMember;

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
