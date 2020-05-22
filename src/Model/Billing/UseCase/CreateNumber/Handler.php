<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\CreateNumber;

use App\Model\Billing\Entity\Account\Number;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private Flusher $flusher;

    private EntityManagerInterface $em;

    public function __construct(
        Flusher $flusher,
        EntityManagerInterface $em
    ) {
        $this->flusher = $flusher;
        $this->em = $em;
    }

    public function __invoke(Command $command)
    {
        $this->em->persist(new Number($command->number));
        $this->flusher->flush();
    }
}
