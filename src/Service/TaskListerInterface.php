<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;

interface TaskListerInterface
{
    public function getFilteredAndSortedTasks(TaskListQuery $query): PaginatedResult;
}
