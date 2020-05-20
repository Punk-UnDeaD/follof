<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\AddSipAccount;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Repository\MemberRepository;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Billing\Service\SipLoginGenerator;
use App\Model\Billing\UseCase\Member\BaseHandlerTrait;
use App\Model\Flusher;
use App\Model\User\Service\HumanStrongPasswordGenerator;

class Handler
{
    use BaseHandlerTrait{
        __construct as private baseConstruct;
    }

    private SipLoginGenerator $loginGenerator;

    private HumanStrongPasswordGenerator $passwordGenerator;

    public function __construct(
        MemberRepository $repository,
        Flusher $flusher,
        SipLoginGenerator $loginGenerator,
        HumanStrongPasswordGenerator $passwordGenerator
    ) {
        $this->baseConstruct($repository, $flusher);
        $this->loginGenerator = $loginGenerator;
        $this->passwordGenerator = $passwordGenerator;
    }

    protected function handle(Member $member): void
    {
        new SipAccount(
            $member,
            $this->loginGenerator->generate($member),
            $this->passwordGenerator->generate()
        );
    }
}
