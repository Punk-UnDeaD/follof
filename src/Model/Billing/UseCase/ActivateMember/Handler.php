<?php

namespace App\Model\Billing\UseCase\ActivateMember;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class Handler
{
    private $flusher;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(Member::class);
        $this->flusher = $flusher;
    }

    public function __invoke(Command $command): void
    {
        /** @var Member $member */
        if ($member = $this->repository->find($command->member_id)) {
            $member->activate();
            $this->flusher->flush();

            return;
        }

    }
}
