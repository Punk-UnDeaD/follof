<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\VoiceMenu;

use App\Model\Billing\Entity\Account\VoiceMenu;
use App\Model\Flusher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\Assert\Assert;

trait BaseHandlerTrait
{
    private Flusher $flusher;
    private ObjectRepository $repository;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, ValidatorInterface $validator)
    {
        $this->repository = $em->getRepository(VoiceMenu::class);
        $this->flusher = $flusher;
        $this->validator = $validator;
    }

    public function __invoke($command): void
    {
        $violationList = $this->validator->validate($command);
        if ($violationList->count()) {
            $message = join(
                '/n',
                array_map(
                    fn ($v) => join('/n', array_map(fn ($v) => $v->getMessage(), (array) $v)),
                    (array) $violationList
                )
            );
            Assert::true(false, $message);
        }

        /** @var VoiceMenu $voiceMenu */
        if (!$voiceMenu = $this->repository->find($command->id)) {
            throw new EntityNotFoundException("VoiceMenu {$command->id} not found.", [$command->id]);
        }
        $this->handle($voiceMenu, $command);
        $this->flusher->flush($voiceMenu);
    }

    abstract protected function handle(VoiceMenu $voiceMenu, $command): void;
}
