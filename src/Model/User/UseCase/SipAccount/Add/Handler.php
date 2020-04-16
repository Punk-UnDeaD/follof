<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\SipAccount\Add;

use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\SipPasswordHasher;

class Handler
{
    private $users;
    private $flusher;
    /**
     * @var SipPasswordHasher
     */
    private SipPasswordHasher $hasher;

    public function __construct(UserRepository $users, Flusher $flusher, SipPasswordHasher $hasher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->user));

        $user->attachSipAccount(
            $command->login,
            $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}
