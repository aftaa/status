<?php
// src/Specification/CompletedTasksSpecification.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

class CompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.is_completed = :is_completed')
            ->setParameter('is_completed', true);
    }

    public function getKey(): string
    {
        return 'completed';
    }
}
