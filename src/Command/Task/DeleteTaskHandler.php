<?php

namespace App\Command\Task;

use App\Enum\TaskAction;
use App\Event\TaskChangedEvent;
use App\Factory\TaskEventFactory;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
readonly class DeleteTaskHandler
{
    public function __construct(
        public final TaskRepository $taskRepository,
        public final TaskEventFactory $taskEventFactory,
        public final MessageBusInterface $eventBus,
    ) {

    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);

        if (!$task) {
            throw new \InvalidArgumentException("Task with id {$command->taskId} not found");
        }

        $this->eventBus->dispatch(
            new TaskChangedEvent(
                $this->taskEventFactory->createFromTask(TaskAction::DELETE, $task),
            ),
        );

        $this->taskRepository->remove($task);
    }
}
