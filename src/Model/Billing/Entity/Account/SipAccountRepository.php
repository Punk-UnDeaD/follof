<?php


namespace App\Model\Billing\Entity\Account;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityNotFoundException;

class SipAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SipAccount::class);
    }

    public function get($id): SipAccount
    {
        /** @var SipAccount $sipAccount */
        if (!$sipAccount = $this->find($id)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier('SipAccount', [$id]);
        }

        return $sipAccount;
    }
}