<?php

namespace App\Command\Task;

use App\Contract\AsyncMessageInterface;
use App\Dto\TaskDto;

readonly class UpdateTaskCommand implements AsyncMessageInterface
{
    public function __construct(
        public int     $taskId,
        public TaskDto $taskData,
    ) {}
}
