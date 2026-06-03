<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Entity\Task;

final class TaskFactory
{
    public function createFromDto(TaskDto $dto): Task
    {
        $task = new Task();
        $task->setName($dto->name);
        $task->setStatus($dto->status);

        return $task;
    }

    public function updateFromDto(Task $task, TaskDto $dto): void
    {
        $task->setName($dto->name);
        $task->setStatus($dto->status);
    }
}
