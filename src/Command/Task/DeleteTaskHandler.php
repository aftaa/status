<?php

namespace App\Command\Task;

use App\Entity\Task;
use App\Event\TaskLoggedEvent;
use App\Factory\TaskEventFactory;
use App\Repository\TaskRepository;
use App\Service\TaskEventLogger;
use Doctrine\ORM\EntityManagerInterface;
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
            new TaskLoggedEvent(
                $this->taskEventFactory->createFromTask('delete', $task),
            ),
        );

        $this->taskRepository->remove($task);
    }
}
