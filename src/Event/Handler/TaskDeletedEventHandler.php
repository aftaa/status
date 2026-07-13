<?php

namespace App\Event\Handler;

use App\Enum\TaskAction;
use App\Event\TaskDeletedEvent;
use App\Factory\TaskEventFactory;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class TaskDeletedEventHandler
{
    public function __construct(
        private TaskEventFactory $taskEventFactory,
        private TaskEventLogger $logger,
    ) {}

    public function __invoke(TaskDeletedEvent $event): void
    {
        $taskEvent = $this->taskEventFactory->createFromDto(
            TaskAction::DELETE,
            $event->taskData,
        );

        $this->logger->log($taskEvent);
    }
}
