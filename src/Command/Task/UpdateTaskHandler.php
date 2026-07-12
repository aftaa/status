<?php

namespace App\Command\Task;

use App\Enum\TaskAction;
use App\Event\TaskChangedEvent;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class UpdateTaskHandler
{
    public function __construct(
        private TaskFactory         $taskFactory,
        private TaskEventFactory    $taskEventFactory,
        private MessageBusInterface $eventBus,
        private TaskRepository      $taskRepository,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(UpdateTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $this->taskFactory->updateFromDto($task, $command->taskData);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(
            new TaskChangedEvent(
                $this->taskEventFactory->createFromTask(TaskAction::EDIT, $task, $command->taskData),
            ),
        );
    }
}
