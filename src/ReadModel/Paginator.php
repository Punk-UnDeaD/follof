<?php

declare(strict_types=1);

namespace App\ReadModel;

use Doctrine\DBAL\Query\QueryBuilder;

class Paginator implements \Countable, \Iterator
{

    private QueryBuilder $query;
    private QueryBuilder $countQuery;

    private ?int $count = null;
    private ?array $data = null;
    private int $pageSize;
    private int $page;
    private int $index = 0;

    public function __construct(QueryBuilder $queryBuilder, int $page = 1, int $pageSize = 10)
    {
        $this->query = (clone $queryBuilder)->setMaxResults($pageSize)
            ->setFirstResult(($page - 1) * $pageSize);
        $this->countQuery = (clone $queryBuilder)
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->resetQueryPart('orderBy')
            ->select('count(*)');
        $this->pageSize = $pageSize;
        $this->page = $page;
    }

    public function getItems()
    {
        $ind = ($this->page - 1) * $this->pageSize;
        foreach ($this->data as $item) {
            yield ++$ind => $item;
        }
    }

    public function getPageCount(): int
    {
        return (int)ceil($this->count() / $this->pageSize);
    }

    public function count(): int
    {
        return $this->count ?? $this->count = $this->countQuery->execute()->fetchColumn();
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function current()
    {
        return $this->data()[$this->index];
    }

    private function data(): array
    {
        return $this->data ?? $this->data = $this->query->execute()->fetchAll();
    }

    public function next()
    {
        $this->index++;
    }

    public function key()
    {
        return $this->index + 1 + $this->getOffset();
    }

    public function getOffset()
    {
        return ($this->page - 1) * $this->pageSize;
    }

    public function valid(): bool
    {
        return $this->index < count($this->data());
    }

    public function rewind()
    {
        $this->index = 0;
    }
}
