<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Dto\TaskEvent;
use App\Entity\Task;
use App\Enum\TaskAction;

final class TaskEventFactory
{
    public function createFromTask(TaskAction $action, Task $task, ?TaskDto $payload = null): TaskEvent
    {
        $oldState = $task->toArray();
        $newState = $payload?->toArray() ?? $oldState;

        return new TaskEvent(
            taskId: $task->getId(),
            action: $action->value,
            oldData: $oldState,
            newData: $newState,
        );
    }
}
