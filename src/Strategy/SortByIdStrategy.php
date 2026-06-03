<?php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

class SortByIdStrategy implements SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void
    {
        $qb->orderBy('t.id', $order);
    }

    public function getKey(): string
    {
        return 'id';
    }
}
