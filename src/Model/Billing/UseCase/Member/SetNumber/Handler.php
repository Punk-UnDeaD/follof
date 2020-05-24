<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\SetNumber;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\Repository\MemberRepository;
use App\Model\Billing\UseCase\Member\BaseHandlerTrait;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class Handler
{
    use BaseHandlerTrait {
        __construct as private baseConstruct;
    }

    private ObjectRepository $numberRepository;

    public function __construct(MemberRepository $repository, Flusher $flusher, EntityManagerInterface $em)
    {
        $this->baseConstruct($repository, $flusher);
        $this->numberRepository = $em->getRepository(Number::class);
    }

    protected function handle(Member $member, Command $command): void
    {
        /** @var Number $number */
        if ($command->number && !$number = $this->numberRepository->find($command->number)) {
            throw new \LogicException('Number not found');
        }
        $member->setNumber($command->number ? $number : null);
    }
}
