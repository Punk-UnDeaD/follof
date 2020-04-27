<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount\UpdateLogin;

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
        $sipAccount = $this->repository->get($command->sipAccount_id);
        $sipAccount->setLogin($command->login);
        $this->flusher->flush($sipAccount);

        return;
    }
}
