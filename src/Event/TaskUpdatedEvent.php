<?php

namespace App\Event;

use App\Contract\AsyncMessageInterface;
use App\Dto\TaskDto;

final readonly class TaskUpdatedEvent implements AsyncMessageInterface
{
    public function __construct(
        public TaskDto $oldData,
        public TaskDto $newData,
    ) {}
}
