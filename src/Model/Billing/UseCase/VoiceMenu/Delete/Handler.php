<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu\Delete;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Billing\UseCase\VoiceMenu\BaseHandlerTrait;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Handler
{
    use BaseHandlerTrait {
        __construct as traitConstruct;
    }

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, ValidatorInterface $validator)
    {
        $this->traitConstruct($em, $flusher, $validator);
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
