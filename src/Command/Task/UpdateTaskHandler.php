<?php

namespace App\Command\Task;

use App\Enum\TaskAction;
use App\Event\BaseTaskUpdatedEvent;
use App\Factory\TaskDtoFactory;
use App\Factory\TaskEventFactory;
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

        $oldState = clone $task;

        $this->taskFactory->updateFromDto($task, $command->taskData);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(new BaseTaskUpdatedEvent($task->getId(), $oldState->toArray(), $task->toArray()));
    }
}
