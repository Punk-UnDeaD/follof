<?php

namespace App\ReadModel\Billing;

use App\Model\Billing\Entity\Account\Member;
use App\Model\Billing\Entity\Account\Role;
use App\Model\User\Entity\User\User;
use App\ReadModel\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class MemberFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;
    /**
     * @var TagAwareCacheInterface
     */
    private TagAwareCacheInterface $cachePool;

    public function __construct(Connection $connection, EntityManagerInterface $em, TagAwareCacheInterface $myCachePool)
    {
        $this->connection = $connection;
        $this->em = $em;
        $this->repository = $em->getRepository(Member::class);
        $this->cachePool = $myCachePool;
    }
    private function ownerQuery(){
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
        $query = $this->connection->createQueryBuilder()
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

        return $query;
    }

    private function getResult($query): ?AuthView
    {
        $query = $query->execute();
        $query->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        return $query->fetch() ?: null;
    }

    public function loadById($id): ?AuthView
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
                $result = $this->getResult($query);
                $item->tag([$result->user_id, $result->id]);

                return $result;
            }
        );
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

        return $this->getResult($query);
    }

    public function loadOwnerByLogin($login): ?AuthView
    {
        $query = $this->ownerQuery()
            ->andWhere('users.email = :login')
            ->setParameter(':login', $login);

        return $this->getResult($query);
    }

    public function loadOwnerByBillingId($login): ?AuthView
    {
        $query = $this->ownerQuery()
            ->andWhere('team.billing_id = :login')
            ->setParameter(':login', $login);

        return $this->getResult($query);
    }

    public function get($getId): Member
    {
        if (($member = $this->repository->find($getId)) && $member instanceof Member) {
            return $member;
        }

        throw new NotFoundException('Member is not found');
    }

}
