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
readonly class CreateTaskHandler
{
    public function __construct(
        private TaskRepository      $taskRepository,
        private TaskFactory         $taskFactory,
        private TaskEventFactory    $taskEventFactory,
        private MessageBusInterface $eventBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateTaskCommand $command): void
    {
        $task = $this->taskFactory->createFromDto($command->taskData);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(
            new TaskChangedEvent(
                $this->taskEventFactory->createFromTask(
                    TaskAction::CREATE,
                    $task,
                ),
            ),
        );
    }
}
