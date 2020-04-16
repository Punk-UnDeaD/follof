<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\User;
use Doctrine\DBAL\Connection;

class SipLoginGenerator
{
    const MASK = '/^(?<prefix>\w{6})-\d{5}/';

    private array $used_prefix = [];
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * SipLoginGenerator constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function generate(User $user): string
    {
        $prefix = $this->getExistingPrefix($user) ?? $this->generatePrefix($user);
        $suffix = count($user->getSipAccounts());
        $suffix = sprintf("%'.05d", $suffix);
        $this->used_prefix[] = $prefix;

        return "{$prefix}-{$suffix}";
    }

    private function getExistingPrefix(User $user): ?string
    {
        $sipAccounts = $user->getSipAccounts();

        if ($sipAccounts) {
            $existing_login = $sipAccounts[0]->getLogin();
            if (preg_match(self::MASK, $existing_login, $matches)) {
                return $matches['prefix'];
            }
        }

        return null;
    }

    private function generatePrefix(): string
    {
        do {
            $prefix = sprintf("%'.06d", rand(0, 999999));
        } while (!$this->isPrefixNotUsed($prefix));

        return $prefix;
    }

    private function isPrefixNotUsed($prefix)
    {
        if (in_array($prefix, $this->used_prefix)) {
            return false;
        }

        $in_db = $this->connection->createQueryBuilder()
            ->select('count(id)')
            ->from('user_user_sip_accounts')
            ->where('login LIKE :prefix')
            ->setParameter(':prefix', "{$prefix}-%")
            ->execute()
            ->fetchColumn();

        return !$in_db;
    }
}
