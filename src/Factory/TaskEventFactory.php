<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Dto\TaskEvent;
use App\Entity\Task;

final class TaskEventFactory
{
    public function createFromTask(string $action, Task $task, ?TaskDto $newData = null): TaskEvent
    {
        $oldData = $task->toArray();

        return new TaskEvent(
            taskId: $task->getId() ?? 0,
            action: $action,
            oldData: $oldData,
            newData: $newData ? $newData->toArray() : $oldData,
        );
    }
}
