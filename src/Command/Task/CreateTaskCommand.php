<?php

namespace App\Command\Task;

use App\Dto\TaskDto;

readonly class CreateTaskCommand
{
    public function __construct(
        public TaskDto $dto,
    ) {}
}
