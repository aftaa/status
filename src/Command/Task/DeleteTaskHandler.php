<?php

namespace App\Command\Task;

use App\Entity\Task;
use App\Factory\TaskEventFactory;
use App\Repository\TaskRepository;
use App\Service\TaskEventLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class DeleteTaskHandler
{
    public function __construct(
        public final TaskRepository $taskRepository,
        public final EntityManagerInterface $entityManager,
        public final TaskEventLogger $taskEventLogger,
        public final TaskEventFactory $taskEventFactory,
    ) {

    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);

        if (!$task) {
            throw new \InvalidArgumentException("Task with id {$command->taskId} not found");
        }

        $this->taskEventLogger->log($this->taskEventFactory->createFromTask('delete', $task));

        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }
}
