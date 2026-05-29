<?php
// src/Strategy/SortByCreatedAtStrategy.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

class SortByCreatedAtStrategy implements SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void
    {
        $qb->orderBy('t.created_at', $order);
    }

    public function getKey(): string
    {
        return 'created_at';
    }
}
