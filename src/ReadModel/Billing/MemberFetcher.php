<?php

declare(strict_types=1);

namespace App\ReadModel\Billing;

use App\Model\Billing\Entity\Account\DataType\Role;
use App\Model\Billing\Entity\Account\Member;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MemberFetcher
{
    private Connection $connection;

    private TagAwareCacheInterface $cachePool;

    public function __construct(Connection $connection, TagAwareCacheInterface $myCachePool)
    {
        $this->connection = $connection;
        $this->cachePool = $myCachePool;
    }

    private function ownerQuery()
    {
        return $this->query()
            ->addSelect(
                'users.email as login',
                'users.password_hash as password_hash',
            )
            ->where('member.role = :role')
            ->setParameter(':role', Role::OWNER);
    }

    private function query()
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'member.id as id',
                'team.id as team_id',
                'users.id as user_id',
                'users.status as user_status',
                'member.status as member_status',
                'member.role as role'
            )
            ->from('billing_members', 'member')
            ->join('member', 'billing_team', 'team', 'member.team_id=team.id')
            ->join('team', 'user_users', 'users', 'team.user_id=users.id')
            ->setMaxResults(1);
    }

    private function getAuthViewResult(QueryBuilder $query): ?AuthView
    {
        $query = $query->execute();
        $query->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        return $query->fetch() ?: null;
    }

    public function findAuthView($id): ?AuthView
    {
        return $this->cachePool->get(
            md5(AuthView::class.':'.$id),
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(50);
                $query = $this->query()
                    ->addSelect(
                        'CASE '.
                        'WHEN member.login IS NOT NULL THEN CONCAT(member.login, \'@\', team.billing_id) '.
                        '    ELSE users.email '.
                        '    END as login',
                        'COALESCE(member.password_hash, users.password_hash) as password_hash',
                    )
                    ->where('member.id = :id')
                    ->setParameter(':id', $id);
                $result = $this->getAuthViewResult($query);
                if ($result) {
                    $item->tag([$result->user_id, $result->id]);
                } else {
                    $item->expiresAfter(0);
                }

                return $result;
            }
        );
    }

    public function getAuthView($id): AuthView
    {
        if (!$result = $this->findAuthView($id)) {
            throw new NotFoundException('Member not found.');
        }

        return $result;
    }

    public function loadByLogin($login): ?AuthView
    {
        return $this->loadOwnerByLogin($login)
            ?? $this->loadOwnerByBillingId($login)
            ?? $this->loadMemberByLogin($login);
    }

    public function loadMemberByLogin($login): ?AuthView
    {
        $login = explode('@', $login);

        if (count($login) < 2) {
            return null;
        }
        $query = $this->query()
            ->addSelect(
                'CONCAT(member.login, \'@\', team.billing_id) as login',
                'member.password_hash as password_hash',
            )
            ->andWhere('team.billing_id = :billing_id')
            ->andWhere('member.login = :login')
            ->setParameter(':login', $login[0])
            ->setParameter(':billing_id', $login[1]);

        return $this->getAuthViewResult($query);
    }

    public function loadOwnerByLogin($login): ?AuthView
    {
        $query = $this->ownerQuery()
            ->andWhere('users.email = :login')
            ->setParameter(':login', $login);

        return $this->getAuthViewResult($query);
    }

    public function loadOwnerByBillingId($login): ?AuthView
    {
        $query = $this->ownerQuery()
            ->andWhere('team.billing_id = :login')
            ->setParameter(':login', $login);

        return $this->getAuthViewResult($query);
    }

    public function get($getId): Member
    {
        if (($member = $this->repository->find($getId)) && $member instanceof Member) {
            return $member;
        }

        throw new NotFoundException('Member is not found');
    }
}
