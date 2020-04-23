<?php

namespace App\Model\Billing\UseCase\ActivateSipAccount;

use App\Model\Billing\Entity\Account\SipAccountRepository;
use App\Model\Flusher;

class Handler
{
    private Flusher $flusher;
    private SipAccountRepository $repository;

    public function __construct(SipAccountRepository $repository, Flusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
    }

    public function __invoke(Command $command): void
    {
        $this->repository->get($command->sipAccount_id)->activate();
        $this->flusher->flush();

        return;
    }
}
