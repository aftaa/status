<?php

namespace App\Dto;

use App\Enum\TaskFilter;
use App\Enum\TaskOrder;
use App\Enum\TaskSort;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;

readonly class TaskListQuery
{
    public function __construct(
        public TaskSort   $sort = TaskSort::CREATED_AT,
        public TaskOrder  $order = TaskOrder::ASC,
        public TaskFilter $filter = TaskFilter::ALL,
        public Page       $page = new Page(1),
        public Limit      $limit = new Limit(10),
    ) {}
}
