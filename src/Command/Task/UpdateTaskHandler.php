<?php

namespace App\Command\Task;

use App\Command\Task\UpdateTaskCommand;
use App\Entity\Task;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Service\TaskEventLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class UpdateTaskHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskFactory            $taskFactory,
        private TaskEventFactory       $taskEventFactory,
        private TaskEventLogger        $taskEventLogger,
    ) {}

    public function __invoke(UpdateTaskCommand $command): void
    {
        $task = $this->entityManager->getRepository(Task::class)->find($command->id);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $this->taskFactory->updateFromDto($task, $command->dto);
        $this->entityManager->flush();

        $this->taskEventLogger->log(
            $this->taskEventFactory->createFromTask('edit', $task, $command->dto)
        );
    }
}
