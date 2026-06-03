<?php

namespace App\Factory;

use App\Dto\TaskListQuery;
use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use Symfony\Component\HttpFoundation\Request;

final class TaskListQueryFactory
{
    final public const int PAGE = 1;
    final public const int LIMIT = 20;

    public function fromRequest(Request $request): TaskListQuery
    {
        return new TaskListQuery(
            sort:   TaskSort::tryFrom($request->query->get('sort', TaskSort::DEFAULT)),
            order:  TaskOrder::tryFrom($request->query->get('order', TaskOrder::DEFAULT)),
            filter: TaskFilter::tryFrom($request->query->get('filter', TaskFilter::DEFAULT)),
            page:   new Page($request->query->get('page', self::PAGE)),
            limit:  new Limit($request->query->get('limit', self::LIMIT)),
        );
    }
}
