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
    public function fromRequest(Request $request): TaskListQuery
    {
        return new TaskListQuery(
            sort:   TaskSort::tryFrom($request->query->get('sort', 'created_at')) ?? TaskSort::CREATED_AT,
            order:  TaskOrder::tryFrom($request->query->get('order', 'asc')) ?? TaskOrder::ASC,
            filter: TaskFilter::tryFrom($request->query->get('filter', 'all')) ?? TaskFilter::ALL,
            page:   new Page($request->query->get('page', 1)),
            limit:  new Limit($request->query->get('limit', 20)),
        );
    }
}
