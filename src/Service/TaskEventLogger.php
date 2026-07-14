<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Event\BaseTaskEvent;
use App\ValueObject\Pagination\Limit;
use App\ValueObject\Pagination\Page;
use MongoDB\Client;
use MongoDB\Collection;

final class TaskEventLogger
{
    private Collection $collection;

    public function __construct(Client $client)
    {
        $this->collection = $client->selectCollection(
            $_ENV['MONGODB_DB'],
            'task_events'
        );
    }

    public function log(BaseTaskEvent $event): void
    {
        $this->collection->insertOne([
            'taskId'    => $event->taskId,
            'action'    => $event->action,
            'oldData'   => $event->oldData,
            'newData'   => $event->newData,
            'createdAt' => $event->createdAt->format('c'),
        ]);
    }

    public function findByTaskId(int $taskId): array
    {
        return $this->collection->find(
            ['taskId' => $taskId],
            ['sort' => ['createdAt' => -1]]
        )->toArray();
    }

    public function list(Page $page, Limit $limit): PaginatedResult
    {
        $skip = ($page->number - 1) * $limit->value;

        $items = $this->collection->find(
            [],
            [
                'skip' => $skip,
                'limit' => $limit->value,
                'sort' => ['createdAt' => -1],
            ]
        )->toArray();

        $total = $this->collection->countDocuments();

        return new PaginatedResult(
            items: $items,
            currentPage: $page->number,
            totalItems: (int) $total,
            itemsPerPage: $limit->value,
        );
    }
}
