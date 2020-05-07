<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Flusher;

/**
 * Class AbstractHandler.
 */
trait BaseHandlerTrait
{
    private Flusher $flusher;
    private MemberRepository $repository;

    public function __construct(MemberRepository $repository, Flusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
    }

    public function __invoke($command): void
    {
        $member = $this->repository->get($command->id);
        $this->handle($member, $command);
        $this->flusher->flush($member);
    }

    abstract public function handle(Member $member, $command): void;
}
