<?php

namespace App\Command\Task;

use App\Event\TaskDeletedEvent;
use App\Factory\TaskDtoFactory;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private TaskDtoFactory $taskDtoFactory,
        private MessageBusInterface $eventBus,
    ) {}

    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $taskData = $this->taskDtoFactory->createFromEntity($task);

        $this->eventBus->dispatch(new TaskDeletedEvent(
            taskData: $taskData,
        ));

        $this->taskRepository->remove($task);
    }
}
