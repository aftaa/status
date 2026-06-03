<?php
// src/Specification/NotCompletedTasksSpecification.php

namespace App\Specification;

use App\Enum\TaskStatus;
use Doctrine\ORM\QueryBuilder;

class NotCompletedTasksSpecification implements SpecificationInterface
{
    public function apply(QueryBuilder $qb): void
    {
        $qb->andWhere('t.status = :status')
            ->setParameter('status', TaskStatus::NOT_COMPLETED);    }

    public function getKey(): string
    {
        return 'not_completed';
    }
}
