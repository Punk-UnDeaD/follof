<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\AddSipAccount;

use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\Service\SipLoginGenerator;
use App\Model\Flusher;
use App\Model\User\Service\HumanStrongPasswordGenerator;

class Handler
{
    private Flusher $flusher;

    private SipLoginGenerator $loginGenerator;

    private HumanStrongPasswordGenerator $passwordGenerator;

    public function __construct(
        MemberRepository $repository,
        Flusher $flusher,
        SipLoginGenerator $loginGenerator,
        HumanStrongPasswordGenerator $passwordGenerator
    ) {
        $this->repository = $repository;
        $this->flusher = $flusher;
        $this->loginGenerator = $loginGenerator;
        $this->passwordGenerator = $passwordGenerator;
    }

    public function __invoke(Command $command): void
    {
        $member = $this->repository->get($command->member_id);
        new SipAccount(
            $member,
            $this->loginGenerator->generate($member),
            $this->passwordGenerator->generate()
        );
        $this->flusher->flush();
    }
}
