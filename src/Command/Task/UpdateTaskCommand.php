<?php

namespace App\Command\Task;

use App\Dto\TaskDto;

readonly class UpdateTaskCommand
{
    public function __construct(
        public int     $taskId,
        public TaskDto $taskData,
    ) {}
}
