<?php

namespace App\Command\Task;

use App\Event\TaskUpdatedEvent;
use App\Factory\TaskDtoFactory;
use App\Factory\TaskFactory;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateTaskHandler
{
    public function __construct(
        private TaskFactory $taskFactory,
        private TaskDtoFactory $taskDtoFactory,
        private TaskRepository $taskRepository,
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $oldState = $this->taskDtoFactory->createFromEntity($task);

        $this->taskFactory->updateFromDto($task, $command->taskData);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(new TaskUpdatedEvent(
            oldData: $oldState,
            newData: $command->taskData,
        ));
    }
}
