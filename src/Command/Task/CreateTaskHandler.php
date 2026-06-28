<?php

namespace App\Command\Task;

use App\Command\Task\CreateTaskCommand;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Service\TaskEventLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
readonly class CreateTaskHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskFactory            $taskFactory,
        private TaskEventFactory       $taskEventFactory,
        private TaskEventLogger        $taskEventLogger,
    ) {}

    public function __invoke(CreateTaskCommand $command): void
    {
        $task = $this->taskFactory->createFromDto($command->dto);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->taskEventLogger->log(
            $this->taskEventFactory->createFromTask('create', $task)
        );
    }
}
