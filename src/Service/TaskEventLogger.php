<?php

namespace App\Service;

use App\Dto\TaskEvent;
use MongoDB\Collection;
use MongoDB\Client;

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

    public function log(TaskEvent $event): void
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

    public function list(mixed $page, mixed $limit): array
    {
        $skip = ($page - 1) * $limit;

        $items = $this->collection->find(
            [],
            [
                'skip' => $skip,
                'limit' => $limit,
                'sort' => ['createdAt' => -1],
            ],
        )->toArray();

        $total = $this->collection->countDocuments();

        return [
            'items' => $items,
            'total' => $total,
        ];
    }
}
