<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Team;

use App\Model\Billing\Entity\Account\Team;
use App\Model\EntityNotFoundException;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

trait BaseHandlerTrait
{
    private Flusher $flusher;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(Team::class);
        $this->flusher = $flusher;
    }

    public function __invoke($command): void
    {
        /** @var Team $team */
        if (!$team = $this->repository->find($command->id)) {
            throw new EntityNotFoundException("Team {$command->id} not found.");
        }
        $this->handle($team, $command);
        $this->flusher->flush($team);
    }

    abstract public function handle(Team $voiceMenu, $command): void;
}
