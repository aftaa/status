<?php

namespace App\EventHandler;

use App\Event\TaskLoggedEvent;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler('event.bus')]
readonly class TaskLoggedEventHandler
{
    public function __construct(public final TaskEventLogger $logger)
    {

    }

    public function __invoke(TaskLoggedEvent $event): void
    {
        $this->logger->log($event->event);
    }
}
