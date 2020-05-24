<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\SetNumber;

use App\Model\Billing\Entity\Account\Number;
use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    use BaseHandlerTrait {
        __construct as private baseConstruct;
    }

    private ObjectRepository $numberRepository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, ValidatorInterface $validator)
    {
        $this->baseConstruct($em, $flusher, $validator);
        $this->numberRepository = $em->getRepository(Number::class);
    }

    protected function handle(VoiceMenu $voiceMenu, Command $command): void
    {
        /** @var Number $number */
        if ($command->number && !$number = $this->numberRepository->find($command->number)) {
            throw new \LogicException('Number not found');
        }
        $voiceMenu->setNumber($command->number ? $number : null);
    }
}
