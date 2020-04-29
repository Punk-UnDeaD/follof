<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp\Confirm\ByToken;

use App\Model\Billing\Service\TeamBillingIdGenerator;
use App\Model\Billing\UseCase\CreateTeam;
use App\Model\Flusher;
use App\Model\User\Entity\User\UserRepository;

class Handler
{
    private $users;
    private $flusher;

    private TeamBillingIdGenerator $billingIdGenerator;

    private CreateTeam\Handler $teamHandler;

    public function __construct(UserRepository $users, Flusher $flusher, CreateTeam\Handler $teamHandler)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->teamHandler = $teamHandler;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->users->findByConfirmToken($command->token)) {
            throw new \DomainException('Incorrect or confirmed token.');
        }

        $user->confirmSignUp();
        ($this->teamHandler)(new CreateTeam\Command($user->getId()->getValue()));

        $this->flusher->flush();
    }
}
