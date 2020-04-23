<?php


namespace App\Model\Billing\UseCase\BlockMember;


use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\Entity\Account\Team;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

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
        $this->repository->get($command->member_id)->block();
        $this->flusher->flush();
    }
}