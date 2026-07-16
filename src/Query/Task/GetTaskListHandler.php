<?php

namespace App\Query\Task;

use App\Dto\PaginatedResult;
use App\Service\TaskListerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class GetTaskListHandler
{
    public function __construct(
        public final TaskListerInterface $taskLister,
    ) {

    }

    public function __invoke(GetTaskListQuery $query): PaginatedResult
    {
        return $this->taskLister->getFilteredAndSortedTasks($query->taskListQuery);
    }
}
