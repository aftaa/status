<?php

namespace App\Event\Handler;

use App\Event\TaskChangedEvent;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler('event.bus')]
readonly class TaskCreatedEventHandler
{
    public function __construct(public final TaskEventLogger $logger)
    {

    }

    public function __invoke(TaskChangedEvent $event): void
    {
        $this->logger->log($event->event);
    }
}
