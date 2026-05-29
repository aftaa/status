<?php
// src/Specification/SpecificationInterface.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

interface SpecificationInterface
{
    public function apply(QueryBuilder $qb): void;
    public function getKey(): string; // completed, not_completed, etc.
}
