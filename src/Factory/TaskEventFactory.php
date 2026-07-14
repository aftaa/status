<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Enum\TaskAction;
use App\Event\BaseTaskEvent;

final class TaskEventFactory
{
    public function createFromDto(
        TaskAction $action,
        TaskDto $newState,
        ?TaskDto $oldState = null,
    ): BaseTaskEvent {
        return new BaseTaskEvent(
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
    ): BaseTaskEvent {
        $oldState = $task->toArray();

        return new BaseTaskEvent(
            taskId: $task->getId(),
            action: $action->value,
            oldData: $oldState,
            newData: $newState?->toArray() ?? $oldState,
        );
    }
}
