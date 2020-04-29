<?php

declare(strict_types=1);

namespace App\Model\Billing\Service;

use App\Model\Billing\Entity\Account\Team;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class TeamBillingIdGenerator
{
    private ObjectRepository $teamRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->teamRepository = $em->getRepository(Team::class);
    }

    public function generate(): string
    {
        do {
            $id = sprintf("%'.06d", rand(0, 999999));
        } while (!$this->isIdNotUsed($id));

        return $id;
    }

    private function isIdNotUsed(string $id): bool
    {
        return !$this->teamRepository->findBy(['billingId' => $id]);
    }
}
