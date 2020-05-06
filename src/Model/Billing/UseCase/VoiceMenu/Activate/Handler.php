<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Activate;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

class Handler
{
    private Flusher $flusher;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(VoiceMenu::class);
        $this->flusher = $flusher;
    }

    public function __invoke(Command $command)
    {
        /** @var VoiceMenu $voiceMenu */
        if (!$voiceMenu = $this->repository->find($command->id)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier('VoiceMenu', [$command->id]);
        }
        $voiceMenu->activate();
        $this->flusher->flush($voiceMenu);
    }
}
