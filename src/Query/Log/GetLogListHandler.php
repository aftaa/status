<?php

namespace App\Query\Log;

use App\Dto\PaginatedResult;
use App\Service\TaskEventLogger;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetLogListHandler
{
    public function __construct(
        private TaskEventLogger $logger,
    ) {

    }

    public function __invoke(GetLogListQuery $query): PaginatedResult
    {
        return $this->logger->list($query->requestQuery->page, $query->requestQuery->limit);
    }
}
