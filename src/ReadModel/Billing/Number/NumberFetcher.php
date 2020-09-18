<?php

declare(strict_types=1);

namespace App\ReadModel\Billing\Number;

use App\ReadModel\Paginator;
use Doctrine\DBAL\Connection;

class NumberFetcher
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function all(int $page, int $size)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from('billing_numbers', 'number')
            ->leftJoin('number', 'billing_team', 'team', 'number.team_id = team.id')
            ->select(
                'number.number as number',
                'team.billing_id as billing_id'
            );

        return new Paginator($qb, $page, $size);
    }

}
