<?php

namespace App\Service;

use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Doctrine\ORM\QueryBuilder;
use App\Dto\PaginatedResult;

class DoctrinePaginator
{
    public function paginate(QueryBuilder $qb, Page $page, Limit $limit, string $idColumn = 'id'): PaginatedResult
    {
        $rootAlias = $qb->getRootAliases()[0] ?? 't';

        $total = (int) (clone $qb)
            ->select(sprintf('COUNT(DISTINCT %s.%s)', $rootAlias, $idColumn))
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
