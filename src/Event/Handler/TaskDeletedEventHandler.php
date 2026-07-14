<?php

namespace App\Event\Handler;

use App\Event\BaseTaskDeletedEvent;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class TaskDeletedEventHandler
{
    public function __construct(
        private TaskEventLogger $logger,
    ) {}

    public function __invoke(BaseTaskDeletedEvent $event): void
    {
        $this->logger->log($event);
    }
}
