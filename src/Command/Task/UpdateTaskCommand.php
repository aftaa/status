<?php

namespace App\Command\Task;

use App\Dto\TaskDto;

class UpdateTaskCommand
{
    public function __construct(
        public readonly int $id,
        public readonly TaskDto $dto,
    ) {}
}
