<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\SipAccount;

use App\Model\Billing\Entity\Account\Repository\SipAccountRepository;
use App\Model\Billing\Entity\Account\SipAccount;
use App\Model\Flusher;

trait BaseHandlerTrait
{
    private Flusher $flusher;
    private SipAccountRepository $repository;

    public function __construct(SipAccountRepository $repository, Flusher $flusher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
    }

    public function __invoke($command): void
    {
        $sipAccount = $this->repository->get($command->id);
        $this->handle($sipAccount, $command);
        $this->flusher->flush($sipAccount);
    }

    abstract public function handle(SipAccount $sipAccount, $command): void;
}
