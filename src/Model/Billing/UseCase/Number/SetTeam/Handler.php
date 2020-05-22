<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Number\SetTeam;

use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\Team;
use App\Model\Billing\UseCase\Number\BaseHandlerTrait;
use App\Model\EntityNotFoundException;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class Handler
{
    use BaseHandlerTrait {
        __construct as private baseConstruct;
    }

    private ObjectRepository $teamRepository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->baseConstruct($em, $flusher);
        $this->teamRepository = $em->getRepository(Team::class);
    }

    public function handle(Number $number, Command $command): void
    {
        $oldTeam = $number->getTeam();
        $newTeam = null;
        if ($command->teamBillingId) {
            /** @var Team $newTeam */
            if (!$newTeam = $this->teamRepository->findOneBy(['billingId' => $command->teamBillingId])) {
                throw new \LogicException('Team not found');
            }
        }
        $number->setTeam($newTeam);
        if ($oldTeam) {
            $this->flusher->flush($oldTeam);
        }
        if ($newTeam) {
            $this->flusher->flush($newTeam);
        }
    }
}
