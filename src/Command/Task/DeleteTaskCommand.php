<?php

namespace App\Command\Task;

readonly class DeleteTaskCommand
{
    public function __construct(
        public final int $taskId,
    ) {

    }
}
