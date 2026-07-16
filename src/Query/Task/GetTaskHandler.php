<?php

namespace App\Query\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
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
