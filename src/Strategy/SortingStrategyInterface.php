<?php
// src/Strategy/SortingStrategyInterface.php

namespace App\Strategy;

use Doctrine\ORM\QueryBuilder;

interface SortingStrategyInterface
{
    public function apply(QueryBuilder $qb, string $order): void;
    public function getKey(): string;
}
