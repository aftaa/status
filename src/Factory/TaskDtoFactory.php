<?php

namespace App\Factory;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Component\HttpFoundation\Request;

class TaskDtoFactory
{
    public function createFromRequest(Request $request): TaskDto
    {
        $payload = $request->getPayload();

        return new TaskDto(
            name: $payload->get('name') ?? '',
            status: TaskStatus::tryFrom($payload->get('status')) ?? TaskStatus::NOT_COMPLETED,
        );
    }

    public function createFromEntity(Task $task): TaskDto
    {
        return new TaskDto(
            name: $task->getName() ?? '',
            status: $task->getStatus(),
        );
    }

    public function createEmpty(): TaskDto
    {
        return new TaskDto();
    }
}
