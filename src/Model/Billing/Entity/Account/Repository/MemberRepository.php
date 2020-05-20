<?php

declare(strict_types=1);

namespace App\Model\Billing\Entity\Account\Repository;

use App\Model\Billing\Entity\Account\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;

class MemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    public function get($id): Member
    {
        /** @var Member $member */
        if (!$member = $this->find($id)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier('Member', [$id]);
        }

        return $member;
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Member
    {
        /** @var Member|null $member */
        $member = parent::find($id, $lockMode, $lockVersion);

        return $member;
    }
}
