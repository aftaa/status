<?php

namespace App\Event\Handler;

use App\Event\BaseTaskUpdatedEvent;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class TaskUpdatedEventHandler
{
    public function __construct(
        private TaskEventLogger $logger,
    ) {}

    public function __invoke(BaseTaskUpdatedEvent $event): void
    {
        $this->logger->log($event);
    }
}
