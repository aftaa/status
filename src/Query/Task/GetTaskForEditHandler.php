<?php

namespace App\Query\Task;

use App\Dto\TaskDto;
use App\Entity\Task;
use App\Query\Task\GetTaskForEditQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class GetTaskForEditHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(GetTaskForEditQuery $query): TaskDto
    {
        $task = $this->entityManager->getRepository(Task::class)->find($query->id);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        return new TaskDto(
            name: $task->getName() ?? '',
            status: $task->getStatus(),
        );
    }
}
