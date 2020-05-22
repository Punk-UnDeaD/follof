<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Number;

use App\Model\Billing\Entity\Account\Number;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

/**
 * Class AbstractHandler.
 */
trait BaseHandlerTrait
{
    private Flusher $flusher;
    private \Doctrine\Persistence\ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(Number::class);
        $this->flusher = $flusher;
    }

    public function __invoke($command): void
    {
        /** @var Number $number */
        if(!$number = $this->repository->find($command->number)){
            throw new EntityNotFoundException(Number::class, [$command->number]);
        }

        $this->handle($number, $command);
        $this->flusher->flush($number);
    }

    abstract public function handle(Number $number, $command): void;
}
