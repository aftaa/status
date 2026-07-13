<?php

namespace App\Query\Log;

use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler('query.bus')]
final readonly class GetLogByTaskHandler
{
    public function __construct(
        private TaskEventLogger $logger,
    ) {
    }

    public function __invoke(GetLogByTaskQuery $query): array
    {
        return $this->logger->findByTaskId($query->taskId);
    }
}
