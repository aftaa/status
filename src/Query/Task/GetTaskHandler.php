<?php

namespace App\Query\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;

readonly class GetTaskHandler
{
    public function __construct(
        private final TaskRepository $taskRepository,
    ) {

    }

    public function __invoke(GetTaskQuery $query): Task
    {
        return $this->taskRepository->find($query->taskId);
    }
}
