<?php

namespace App\Service;

use App\Dto\PaginatedResult;
use App\Dto\TaskListQuery;
use App\Service\TaskListerInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CachedTaskLister implements TaskListerInterface
{
    public function __construct(
        private TaskListerInterface $delegate,
        private CacheInterface      $cache,
        private LoggerInterface     $logger,
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getFilteredAndSortedTasks(TaskListQuery $query): PaginatedResult
    {
        $cacheKey = sprintf(
            'tasks_list_%s_%s_%s_p%s_l%s',
            $query->filter->value,
            $query->sort->value,
            $query->order->value,
            $query->page,
            $query->limit,
        );

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($query) {
                $item->expiresAfter(600);
                $item->tag(['tasks_collection']);

                return $this->delegate->getFilteredAndSortedTasks($query);
            });
        } catch (\Throwable $exception) {
            $this->logger->error('Redis error', [
                'message' => $exception->getMessage(),
                'cacheKey' => $cacheKey,
            ]);

            return $this->delegate->getFilteredAndSortedTasks($query);
        }
    }
}
