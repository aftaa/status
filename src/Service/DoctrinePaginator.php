<?php

namespace App\Service;

use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Doctrine\ORM\QueryBuilder;
use App\Dto\PaginatedResult;

class DoctrinePaginator
{
    public function paginate(QueryBuilder $qb, Page $page, Limit $limit): PaginatedResult
    {
        $total = (clone $qb)->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->setFirstResult(($page->number - 1) * $limit->value)
            ->setMaxResults($limit->value);

        return new PaginatedResult(
            items: $qb->getQuery()->getResult(),
            currentPage: $page->number,
            totalItems: (int) $total,
            itemsPerPage: $limit->value,
        );
    }
}
