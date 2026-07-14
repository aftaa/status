<?php

namespace App\Command\Task;

use App\Enum\TaskAction;
use App\Event\BaseTaskCreatedEvent;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Repository\TaskRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskFactory $taskFactory,
        private TaskRepository $taskRepository,
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateTaskCommand $command): void
    {
        $task = $this->taskFactory->createFromDto($command->taskData);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(new BaseTaskCreatedEvent($task->getId(), [], $task->toArray()));
    }
}
