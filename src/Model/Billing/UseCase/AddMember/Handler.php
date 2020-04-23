<?php


namespace App\Model\Billing\UseCase\AddMember;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Team;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class Handler
{
    private Flusher $flusher;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(Team::class);
        $this->flusher = $flusher;
    }

    public function __invoke(Command $command): void
    {
        /** @var Team $team */
        if ($team = $this->repository->find($command->team_id)) {
            new Member($team);
            $this->flusher->flush();
            return;
        }

    }
}