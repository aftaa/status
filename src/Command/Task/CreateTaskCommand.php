<?php

namespace App\Command\Task;

use App\Contract\AsyncMessageInterface;
use App\Dto\TaskDto;

readonly class CreateTaskCommand implements AsyncMessageInterface
{
    public function __construct(
        public final TaskDto $taskData,
    ) {}
}
