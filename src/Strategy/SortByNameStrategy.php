<?php
// src/Strategy/SortByNameStrategy.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

class SortByNameStrategy implements SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void
    {
        $qb->orderBy('t.name', $order);
    }

    public function getKey(): string
    {
        return 'name';
    }
}
