<?php


namespace App\Model\Billing\UseCase\UpdateMemberCredentials;


use App\Model\Billing\Entity\Account\MemberRepository;
use App\Model\Flusher;
use App\Model\User\Service\PasswordHasher;
use Webmozart\Assert\Assert;

class Handler
{
    private Flusher $flusher;
    private MemberRepository $repository;
    private PasswordHasher $hasher;

    public function __construct(MemberRepository $repository, Flusher $flusher, PasswordHasher $hasher)
    {
        $this->repository = $repository;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
    }

    public function __invoke(Command $command)
    {
        $member = $this->repository->get($command->member_id);
        if (!$command->login) {
            $member->removeCredentials();
        } else {
            $passwordHash = $command->password ? $this->hasher->hash($command->password) : $member->getPasswordHash();
            Assert::notEmpty($passwordHash, 'Can\'t set empty password.');
            $member->setCredentials($command->login, $passwordHash);
        }
        $this->flusher->flush();
    }
}