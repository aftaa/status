<?php

namespace App\Event;

class BaseTaskEvent
{
    public function __construct(
        public ?int               $taskId,
        public string             $action,
        public array              $oldData,
        public array              $newData,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {

    }
}
