<?php

namespace App\Query\Log;

final readonly class GetLogByTaskQuery
{
    public function __construct(
        public int $taskId,
    ) {

    }
}
