<?php

namespace App\Query\Task;

class GetTaskForEditQuery
{
    public function __construct(
        public readonly int $id,
    ) {}
}
