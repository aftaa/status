<?php

namespace App\Command\Task;

use App\Contract\AsyncMessageInterface;

readonly class DeleteTaskCommand implements AsyncMessageInterface
{
    public function __construct(
        public final int $taskId,
    ) {

    }
}
