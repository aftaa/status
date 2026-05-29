<?php

// src/EventListener/TaskCacheListener.php
namespace App\EventListener;

use App\Entity\Task;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class TaskCacheListener
{
    public function __construct(
        private TagAwareCacheInterface $cache
    ) {}

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
            $this->cache->invalidateTags(['tasks_collection']);
        }
    }
}
