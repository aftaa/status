<?php

// src/EventListener/TaskCacheListener.php
namespace App\Event\Listener;

use App\Entity\Task;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

readonly class TaskCacheListener
{
    public function __construct(
        private TagAwareCacheInterface $cache,
        private LoggerInterface        $logger,
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->invalidate($args->getObject());
    }

    private function invalidate(object $entity): void
    {
        if ($entity instanceof Task) {
            try {
                $this->cache->invalidateTags(['tasks_collection']);
            } catch (\Throwable $e) {
                $this->logger->error('Redis error', [
                    'message' => $e->getMessage(),
                    'cacheKey' => $entity->getId(),
                ]);
            }
        }
    }
}
