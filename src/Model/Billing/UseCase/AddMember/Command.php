<?php


namespace App\Model\Billing\UseCase\AddMember;


class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $team_id;

    public function __construct(string $team_id)
    {
        $this->team_id = $team_id;
    }
}
