<?php

namespace App\Dto;

readonly class TaskEvent
{
    public function __construct(
        public int                $taskId,
        public string             $action,
        public array              $oldData,
        public array              $newData,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
    ) {

    }
}
