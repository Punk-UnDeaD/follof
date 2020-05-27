<?php

declare(strict_types=1);

namespace App\ReadModel\Billing\Team;

use App\ReadModel\Billing\Team\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TeamFetcher
{
    private Connection $connection;

    private PaginatorInterface $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('billing_team', 'team')
            ->join('team', 'user_users', 'users', 'team.user_id = users.id')
            ->select(
                'team.id as id',
                'team.billing_id as billing_id',
                'team.balance_value as balance',
                'users.email as email'
            );
        if ($filter->billingId) {
            $qb->andWhere($qb->expr()->like('billing_id', ':billingId'));
            $qb->setParameter(':billingId', '%'.mb_strtolower($filter->billingId).'%');
        }

        if ($filter->email) {
            $qb->andWhere($qb->expr()->like('LOWER(email)', ':email'));
            $qb->setParameter(':email', '%'.mb_strtolower($filter->email).'%');
        }

        if ($filter->balanceMax) {
            $qb->andWhere('balance_value <= :balanceMax');
            $qb->setParameter(':balanceMax', $filter->balanceMax);
        }
        if ($filter->balanceMin) {
            $qb->andWhere('balance_value => :balanceMin');
            $qb->setParameter(':balanceMin', $filter->balanceMin);
        }

        if (!\in_array($sort, ['billing_id', 'email', 'balance'], true)) {
            throw new \UnexpectedValueException('Cannot sort by '.$sort);
        }

        $qb->orderBy($sort, 'desc' === $direction ? 'desc' : 'asc');

        return $this->paginator->paginate($qb, $page, $size);
    }
}
