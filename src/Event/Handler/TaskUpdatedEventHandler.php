<?php

namespace App\Event\Handler;

use App\Enum\TaskAction;
use App\Event\TaskUpdatedEvent;
use App\Factory\TaskEventFactory;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class TaskUpdatedEventHandler
{
    public function __construct(
        private TaskEventFactory $taskEventFactory,
        private TaskEventLogger $logger,
    ) {}

    public function __invoke(TaskUpdatedEvent $event): void
    {
        $taskEvent = $this->taskEventFactory->createFromDto(
            TaskAction::EDIT,
            $event->newData,
            $event->oldData,
        );

        $this->logger->log($taskEvent);
    }
}
