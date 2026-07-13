<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Dto\TaskEvent;
use App\Enum\TaskAction;

final class TaskEventFactory
{
    public function createFromDto(
        TaskAction $action,
        TaskDto $newState,
        ?TaskDto $oldState = null,
    ): TaskEvent {
        return new TaskEvent(
            taskId: null,
            action: $action->value,
            oldData: $oldState?->toArray() ?? [],
            newData: $newState->toArray(),
        );
    }

    public function createFromEntity(
        TaskAction $action,
        \App\Entity\Task $task,
        ?TaskDto $newState = null,
    ): TaskEvent {
        $oldState = $task->toArray();

        return new TaskEvent(
            taskId: $task->getId(),
            action: $action->value,
            oldData: $oldState,
            newData: $newState?->toArray() ?? $oldState,
        );
    }
}
