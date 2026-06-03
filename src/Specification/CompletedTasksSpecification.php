<?php
// src/Specification/CompletedTasksSpecification.php

namespace App\Specification;

use App\Enum\TaskStatus;
use Doctrine\ORM\QueryBuilder;

class CompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.status = :status')
            ->setParameter('status', TaskStatus::COMPLETED);
    }

    public function getKey(): string
    {
        return 'completed';
    }
}
