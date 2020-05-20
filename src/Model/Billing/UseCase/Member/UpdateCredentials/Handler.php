<?php

declare(strict_types=1);

namespace App\Model\Billing\UseCase\Member\UpdateCredentials;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Repository\MemberRepository;
use App\Model\Billing\UseCase\Member\BaseHandlerTrait;
use App\Model\Flusher;
use App\Model\User\Service\PasswordHasher;
use Webmozart\Assert\Assert;

class Handler
{
    use BaseHandlerTrait {
        __construct as baseConstruct;
    }

    private PasswordHasher $hasher;

    public function __construct(MemberRepository $repository, Flusher $flusher, PasswordHasher $hasher)
    {
        $this->baseConstruct($repository, $flusher);
        $this->hasher = $hasher;
    }

    protected function handle(Member $member, Command $command): void
    {
        if (!$command->login) {
            $member->removeCredentials();
        } else {
            $passwordHash = $command->password ? $this->hasher->hash($command->password) : $member->getPasswordHash();
            Assert::notEmpty($passwordHash, 'Can\'t set empty password.');
            $member->setCredentials($command->login, $passwordHash);
        }
    }
}
