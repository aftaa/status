<?php

namespace App\Command\Task;

use App\Event\TaskLoggedEvent;
use App\Factory\TaskEventFactory;
use App\Factory\TaskFactory;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $task = $this->taskFactory->createFromDto($command->dto);
        $this->taskRepository->save($task);

        $this->eventBus->dispatch(
            new TaskLoggedEvent($this->taskEventFactory->createFromTask('create', $task))
        );
    }
}
