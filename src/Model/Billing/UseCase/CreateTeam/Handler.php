<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\CreateTeam;

use App\Model\Billing\Entity\Account\Team;
use App\Model\Billing\Service\TeamBillingIdGenerator;
use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private UserRepository $users;

    private TeamBillingIdGenerator $billingIdGenerator;

    private Flusher $flusher;

    private EntityManagerInterface $em;

    public function __construct(
        UserRepository $users,
        TeamBillingIdGenerator $billingIdGenerator,
        Flusher $flusher,
        EntityManagerInterface $em
    ) {
        $this->users = $users;
        $this->billingIdGenerator = $billingIdGenerator;
        $this->flusher = $flusher;
        $this->em = $em;
    }

    public function __invoke(Command $command)
    {
        $user = $this->users->get(new Id($command->userId));
        $this->em->persist(new Team($user, $this->billingIdGenerator->generate()));

        $this->flusher->flush();
    }
}
