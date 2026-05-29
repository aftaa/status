<?php
// src/Specification/NotCompletedTasksSpecification.php

namespace App\Specification;

use Doctrine\ORM\QueryBuilder;

class NotCompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.is_completed = :is_completed')
            ->setParameter('is_completed', false);
    }

    public function getKey(): string
    {
        return 'not_completed';
    }
}
