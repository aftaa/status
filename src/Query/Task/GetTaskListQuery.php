<?php

namespace App\Query\Task;

use App\Dto\TaskListQuery;

readonly class GetTaskListQuery
{
    public function __construct(
        public final TaskListQuery $taskListQuery,
    ) {

    }
}
