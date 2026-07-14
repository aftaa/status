<?php

namespace App\Command\Task;

use App\Event\TaskDeletedEvent;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class DeleteTaskHandler
{
    public function __construct(
        private TaskRepository $taskRepository,
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(DeleteTaskCommand $command): void
    {
        $task = $this->taskRepository->find($command->taskId);
        if (!$task) {
            throw new \InvalidArgumentException('Task not found');
        }

        $this->eventBus->dispatch(new TaskDeletedEvent($task->getId(), $task->toArray()));
        $this->taskRepository->remove($task);
    }
}
