<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Create;

use App\Model\Billing\UseCase\CreateTeam;
use App\Model\Flusher;
use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\PasswordGenerator;
use App\Model\User\Service\PasswordHasher;

class Handler
{
    private $users;
    private $hasher;
    private $generator;
    private $flusher;

    private CreateTeam\Handler $teamHandler;

    public function __construct(
        UserRepository $users,
        PasswordHasher $hasher,
        PasswordGenerator $generator,
        Flusher $flusher,
        CreateTeam\Handler $teamHandler
    ) {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->generator = $generator;
        $this->flusher = $flusher;
        $this->teamHandler = $teamHandler;
    }

    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::create(
            Id::next(),
            new \DateTimeImmutable(),
            new Name(
                $command->firstName,
                $command->lastName
            ),
            $email,
            $this->hasher->hash($this->generator->generate())
        );
        $this->users->add($user);

        if (Role::USER !== $command->role) {
            $user->changeRole(new Role($command->role));
        } else {
            ($this->teamHandler)(new CreateTeam\Command($user->getId()->getValue()));
        }

        $this->flusher->flush();
    }
}
