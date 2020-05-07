<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Delete;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\AbstractHandler;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;

class Handler extends AbstractHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, Flusher $flusher)
    {
        parent::__construct($em, $flusher);
        $this->em = $em;
    }

    protected function handle(VoiceMenu $voiceMenu): void
    {
        if (($file = $voiceMenu->getFile()) && is_file($file)) {
            unlink($file);
        }
        $this->em->remove($voiceMenu);
    }
}
