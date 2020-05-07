<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class AbstractHandler.
 *
 * @method void handle(VoiceMenu $voiceMenu, AbstractCommand $command)
 */
abstract class AbstractHandler
{
    private Flusher $flusher;
    private ObjectRepository $repository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        $this->repository = $em->getRepository(VoiceMenu::class);
        $this->flusher = $flusher;
    }

    public function __invoke(AbstractCommand $command): void
    {
        /** @var VoiceMenu $voiceMenu */
        if (!$voiceMenu = $this->repository->find($command->id)) {
            throw new EntityNotFoundException("VoiceMenu {$command->id} not found.", [$command->id]);
        }
        $this->handle($voiceMenu, $command);
        $this->flusher->flush($voiceMenu);
    }
}
