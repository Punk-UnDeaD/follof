<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\ActivateMember;

use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private MemberRepository $repository;

    public function __construct(MemberRepository $repository, Flusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
    }

    public function __invoke(Command $command): void
    {
        $member = $this->repository->get($command->member_id);
        $member->activate();
        $this->flusher->flush($member);

        return;
    }
}
