<?php

namespace App\Event\Handler;

use App\Event\TaskCreatedEvent;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class TaskCreatedEventHandler
{
    public function __construct(
        private TaskEventLogger $logger,
    ) {}

    public function __invoke(TaskCreatedEvent $event): void
    {
        $this->logger->log($event);
    }
}
