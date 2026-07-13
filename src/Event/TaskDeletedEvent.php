<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Dto\TaskDto;

final readonly class TaskDeletedEvent implements AsyncMessageInterface
{
    public function __construct(
        public TaskDto $taskData,
    ) {}
}
